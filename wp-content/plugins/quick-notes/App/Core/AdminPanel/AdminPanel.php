<?php

namespace WBZXTDL\App\Core\AdminPanel;

class AdminPanel
{
    public function renderPage(){
        $html = '<div class="container">';
        $html .= $this->renderAdminPanelTitle();
        $html .= $this->renderAdminPanelForm();
		$html .= '<hr class="mt-3">';
        $html .= '<div class="row mt-3">';
		$html .= $this->renderCreateNoteForm();
        for ($i = 1; $i <= rand(7, 10); $i++){
            $html .= $this->renderAdminNote($i);
        }
        $html .= '</div>';
        $html .= '</div>';
        echo $html;
    }

    public function renderPagination(){

    }

    public function renderAdminPanelTitle(){
        $html = '<div class="row">';
        $html .= '<div class="col text-center">'.__('To Do list settings', 'wbzx-tdl').'</div>';
        $html .= '</div>';
        return $html;
    }

    public function renderAdminPanelForm(){
        $html = '<div class="row">';
        $html .= '<div class="col-xxl-1 col-xl-1 col-lg-1 col-md-6 col-sm-6 col-xs-12">
            <label for="wbzx-rtl">'.__('Enable RTL', '').'</label>
            </div>';
        $html .= '<div class="col-xxl-11 col-xl-11 col-lg-11 col-md-6 col-sm-6 col-xs-12">';
        $html .= '<form id="wbzx-rtl-option" method="POST" action=""><input type="checkbox" name="wbzx-rt" id="wbzx-rtl">
            <button type="submit" class="btn btn-success wbzx-btn-save">'.__('Save', 'wbzx-tdl').'</button>
             <button type="reset" class="btn btn-danger wbzx-btn-reset">'.__('Reset', 'wbzx-tdl').'</button></form>';
        $html .= '</div>';
        return $html;
    }

	public function renderAdminNote(int $id = 0) {
		$editor_id = 'note_editor_' . $id;
		$content = '';
		$settings = [
			'textarea_name' => 'note_content',
			'editor_height' => 200,
			'media_buttons' => false,
			'teeny' => true,
		];

		ob_start();
		?>
		<div class="col-sm-3 wbzx-note">
			<form action="" method="POST" id="note-form-<?php echo esc_attr($id); ?>" class="wbzx-note__form">
				<?php
				wp_nonce_field('save_note_action', 'save_note_nonce');
				?>
				<input
					type="text"
					name="note_title"
					placeholder="<?php esc_attr_e('Your note title', 'wbzx-tdl'); ?>"
					class="wbzx-note__title" disabled="disabled"
				>
				<?php
				wp_editor($content, $editor_id, $settings);

				echo $this->renderBtnsRow();
				?>
			</form>
			<div class="form__result-box" id="result-<?php echo esc_attr($id); ?>"></div>
		</div>
		<?php
		return ob_get_clean();
	}


	public function renderBtnsRow(){
		$html = '<div class="form__wbzx-btns-row mt-3">';
		$html .= $this->renderSaveButton();
		$html .= $this->resetDeleteButton();
		$html .= '</div>';
		return $html;
	}

	public function renderSaveButton(){
		return '<button type="button" class="btn btn-success wbzx-btn wbzx-btn-save">
			<span class="dashicons dashicons-edit"></span>
		</button>';
	}

	public function renderCreateNoteForm(){
		$editorId = 'note_editor_00';
		$content = __('Write your note is here','wbzx-tdl');
		$settings = [
			'textarea_name' => 'new_note_content',
			'editor_height' => 200,
			'media_buttons' => false,
			'teeny' => true,
		];
		ob_start();
		?>
		<div class="col-sm-3 wbzx-note">
			<form action="" method="POST" id="note-form-00" class="wbzx-note-create__form">
				<?php
				wp_nonce_field('save_new_note_action', 'save_note_nonce');
				?>
				<input
					type="text"
					name="note_title"
					placeholder="<?php esc_attr_e('Title your new note', 'wbzx-tdl'); ?>"
					class="wbzx-note__title"
				>
				<?php
				wp_editor($content, $editorId, $settings);
				?>
				<div class="mt-3 flex-row">
					<button type="submit" class="btn btn-success wbzx-btn-save">
						<span class="dashicons dashicons-saved"></span>
					</button>
					<?php echo $this->renderResetButton(); ?>
				</div>
			</form>
			<div class="form__result-box" id="result-00"></div>
		</div>
		<?php
		return ob_get_clean();
	}

	public function renderResetButton(){
		return '<button type="button" class="btn btn-warning wbzx-btn-reset">
			<span class="dashicons dashicons-image-rotate"></span>
		</button>';
	}

	public function resetDeleteButton(){
		return '<button type="button" class="btn btn-danger wbzx-btn-delete">
			<span class="dashicons dashicons-trash"></span>
		</button>';
	}
}