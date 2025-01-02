<?php
namespace SizeConverter\Core\Models\AdminModel;
use SizeConverter\Core\DB\DB;
use SizeConverter\Core\Exception\Exception as Exception;

class AdminModel
{
    private string $table;

    public function __construct()
    {
        global $wpdb;
        $this->table = $wpdb->prefix . 'size_converter_pairs';
    }

    /**
     * @throws Exception
     */
    public function addPair(string $originalSize, int $convertedSize): array
    {
        $originalSize = trim(esc_js(esc_html($originalSize)));
        if (empty($originalSize) || $convertedSize <= 0) {
            throw new Exception('Invalid input data for adding a size pair.');
        }
        return (DB::insert($this->table, [
            'original_size' => $originalSize,
            'converted_size' => $convertedSize,
        ]));
    }

    /**
     * @throws Exception
     */
    public function updatePair(string $originalSize, string $oldOriginalSize, int $convertedSize, int $oldConvertedSize)
    {
        $originalSize = trim(sanitize_text_field($originalSize));
        $oldOriginalSize = trim(sanitize_text_field($oldOriginalSize));
        $convertedSize = intval($convertedSize);
        $oldConvertedSize = intval($oldConvertedSize);

        // Проверка входных данных
        if (!preg_match('/^[a-zA-Z]+$/', $originalSize) || !preg_match('/^[a-zA-Z]+$/', $oldOriginalSize)) {
            wp_send_json_error("Invalid format for original sizes. Only alphabetic characters a-zA-Z are allowed.");
        }

        if ($convertedSize <= 0 || $oldConvertedSize <= 0) {
            wp_send_json_error("Converted sizes must be positive integers.");
        }

        // Обновление записи
        $result = DB::update($this->table, [
            'original_size' => $originalSize,
            'converted_size' => $convertedSize,
        ], [
            'original_size' => $oldOriginalSize,
            'converted_size' => $oldConvertedSize,
        ]);


        if ($result !== false) {
            wp_send_json_success("Pair updated successfully.");
        } else {
            wp_send_json_error("Failed to update the pair. Please check the logs.");
        }
    }

    public function deleteData(string $originalSize, int $convertedSize): bool
    {
        $originalSize = trim(sanitize_text_field($originalSize));

        if (!preg_match('/^[a-zA-Z]+$/', $originalSize)) {
            throw new Exception("Invalid format for original size $originalSize. Only alphabetic characters are allowed.");
        }

        if ($convertedSize <= 0) {
            throw new Exception("Invalid value for converted size $convertedSize. Must be a positive integer.");
        }

        $result = DB::delete($this->table, [
            'original_size' => $originalSize,
            'converted_size' => $convertedSize,
        ]);

        return $result > 0;
    }

    /**
     * @throws Exception
     * @throws \JsonException
     */
    public function getPairs(int $limit = 10, int $offset = 0): string
    {
        $countPairs = intval($this->getTotalPairs());
        $paginate = ($countPairs > 10)? true : false;
        $offset = ($paginate)? $offset : 0;
        $results = [
            'pairs' => DB::read($this->table, ['original_size', 'converted_size'], [], $limit, $offset),
            'count' => $countPairs
        ];
        return json_encode($results, JSON_THROW_ON_ERROR);
    }

    /**
     * @throws Exception
     */
    public function getTotalPairs(): int
    {
        $results = DB::read($this->table, ['COUNT(*) AS total']);
        return (int) ($results[0]['total'] ?? 0);
    }
}
