<?php
/** @var \StarterKit\Mvc\View\ViewData $data */
$settings = $data->get('settings');
?>
<div class="wrap starterkit-mvc-admin">
    <h1><?php echo esc_html__('MVC Starter Settings', 'wordpress-mvc-starter'); ?></h1>
    <?php // phpcs:ignore WordPress.Security.NonceVerification.Recommended -- Read-only success notice. ?>
    <?php if (isset($_GET['updated']) && sanitize_text_field(wp_unslash($_GET['updated'])) === '1') : ?>
        <?php
        // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped -- Component escapes its props.
        echo $data->render('components.notice', ['message' => __('Settings saved.', 'wordpress-mvc-starter')]);
        ?>
    <?php endif; ?>
    <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
        <input type="hidden" name="action" value="starterkit_mvc_save_settings">
        <?php wp_nonce_field('starterkit_mvc_save_settings'); ?>
        <table class="form-table" role="presentation"><tbody>
            <tr><th scope="row"><label for="starterkit-mvc-greeting"><?php echo esc_html__('Greeting', 'wordpress-mvc-starter'); ?></label></th>
                <td><input class="regular-text" id="starterkit-mvc-greeting" name="greeting" maxlength="120" required value="<?php echo esc_attr($settings->greeting); ?>"></td></tr>
            <tr><th scope="row"><?php echo esc_html__('Display greeting', 'wordpress-mvc-starter'); ?></th>
                <td>
                    <input type="hidden" name="enabled" value="0">
                    <label><input type="checkbox" name="enabled" value="1" <?php checked($settings->enabled); ?>>
                        <?php echo esc_html__('Enabled', 'wordpress-mvc-starter'); ?></label>
                </td>
            </tr>
        </tbody></table>
        <?php submit_button(__('Save Settings', 'wordpress-mvc-starter')); ?>
    </form>
</div>
