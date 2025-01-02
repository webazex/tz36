<?php

namespace WBZXTDL\App\Core\Http\AjaxHandlers;

use WBZXTDL\App\Core\Exception\Exception as Exception;
use WBZXTDL\App\Core\Logger\Logger as Log;

class AjaxHandlers
{
    public static function init(): void
    {
        if(is_admin()){
            add_action('wp_ajax_quick_notes_action', [self::class, 'handle']);
        }
        add_action('wp_ajax_nopriv_quick_notes_action', [self::class, 'handle']);
    }

    public static function handle(): void
    {
        try {
            // check nonce in subaction
            $action = sanitize_text_field($_POST['action_type'] ?? '');
            $nonce_name = ($action === 'search_pair') ? 'quick_notes_nonce' : 'size_converter_nonce';

            // check nonce
            if (!check_ajax_referer($nonce_name, 'security', false)) {
                throw new Exception(__('Token security error.', 'wbzx-tdl'));
            }

            // Для админских действий проверяем права
            if ($action !== 'search_pair' && !current_user_can('manage_options')) {
                throw new Exception('You don`t have permissions.');
            }

            // Обработка действия
            $response = match ($action) {
                'get_pairs' => self::handleGetPairs(),
                'add_pair' => self::handleAddPair(),
                'update_pair' => self::handleUpdatePair(),
                'delete_pair' => self::handleDeletePair(),
                'search_pair' => self::handleSearchPair(), // Фронтенд-логика
                default => throw new Exception('Unknown action.'),
            };

            wp_send_json_success($response);
        } catch (Exception $e) {
            // Логирование ошибки и отправка сообщения клиенту
            error_log($e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    private static function handleAddPair()
    {
        $originalSize = ""; //default data
        $convertedSize = 99999; //default data
        if(!empty($_POST['pair'])){
            foreach ($_POST['pair'] as $itemPair) {
                switch ($itemPair['name']){
                    case "original_size":
                        $originalSize = strtolower((sanitize_text_field($itemPair['value'])));
                        break;
                    case "converted_size":
                        $convertedSize = intval(sanitize_text_field($itemPair['value']));
                        break;
                }
            }
        }
        if (empty($originalSize) || $convertedSize <= 0) {
            throw new Exception('Incorrect data');
        }
        $adminController = new AdminController();
        $data = $adminController->createData($originalSize, $convertedSize);
        if(boolval($data['result'])){
            wp_send_json_success($data['msg']);
        }else{
            wp_send_json_error($data['msg']);
        }
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    private static function handleGetPairs() {
        try {

            $limit = (!empty($_POST['limit'])) ? absint($_POST['limit']) : 10; // по умолчанию 10
            $offset = (!empty($_POST['offset'])) ? absint($_POST['offset']) : 0;


            $adminController = new AdminController();
            $data = $adminController->getData($limit, $offset);

            if (!empty($data)) {
                wp_send_json_success($data); // Отправляем данные в формате JSON
            } else {
                wp_send_json_success(['pairs' => [], 'count' => 0]); // Возвращаем пустой массив и нулевой счетчик
            }
        } catch (Exception $e) {

            $log = new Log();
            $log->error($e->getMessage());
            wp_send_json_error(['message' => 'An error occurred']);
        }
    }

    private static function handleUpdatePair() {
        try {
            $originalSize = isset($_POST['original_size']) ? trim(strtolower(sanitize_text_field($_POST['original_size']))) : '';
            $convertedSize = isset($_POST['converted_size']) ? intval(sanitize_text_field($_POST['converted_size'])) : 0;
            $oldOriginalSize = isset($_POST['old_original_size']) ? trim(strtolower(sanitize_text_field($_POST['old_original_size']))) : '';
            $oldConvertedSize = isset($_POST['old_converted_size']) ? intval(sanitize_text_field($_POST['old_converted_size'])) : 0;

            // Проверка данных
            if (empty($originalSize) || !preg_match('/^[a-zA-Z]+$/', $originalSize)) {
                wp_send_json_error("Invalid original size $originalSize.");
            }
            if ($convertedSize <= 0) {
                wp_send_json_error("Failed to update pair $originalSize and $convertedSize. No changes were made.");
            }

            $adminController = new AdminController();
            $updated = $adminController->editData($originalSize, $oldOriginalSize, $convertedSize, $oldConvertedSize);

            if ($updated) {
                wp_send_json_success('Pair updated successfully.');
            } else {
                wp_send_json_error('Failed to update pair. No changes were made.');
            }
        } catch (Exception $e) {
            $log = new Log();
            $log->error($e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    private static function handleDeletePair(): void
    {
        try {
            $originalSize = trim(strtolower(sanitize_text_field($_POST['original_size'])));
            $convertedSize = intval(sanitize_text_field($_POST['converted_size']));

            if (empty($originalSize) || $convertedSize <= 0) {
                throw new Exception('Invalid data provided for deletion.');
            }

            $adminController = new AdminController();
            $result = $adminController->removeData($originalSize, $convertedSize);

            if ($result) {
                wp_send_json_success(['message' => 'Pair successfully deleted.']);
            } else {
                throw new Exception('Failed to delete pair. It might not exist.');
            }
        } catch (Exception $e) {
            error_log($e->getMessage());
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }

    private static function handleSearchPair() {
        try {
            // secure check
            if (!check_ajax_referer('size_converter_search_nonce', 'security', false)) {
                throw new Exception('Invalid nonce');
            }

            // get args
            $originalSize = strtolower(trim(sanitize_text_field($_POST['original_size'])));

            if (!preg_match('/^[a-zA-Z]+$/', $originalSize)) {
                throw new Exception('Invalid input format. Only a-z, A-Z are allowed.');
            }

            // use controller
            $controller = new FrontController();
            $result = $controller->getPair($originalSize);

            if ($result) {
                wp_send_json_success($result);
            } else {
                wp_send_json_error(['message' => 'No matching size found.']);
            }
        } catch (Exception $e) {
            wp_send_json_error(['message' => $e->getMessage()]);
        }
    }




}