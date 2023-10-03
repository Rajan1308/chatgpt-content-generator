<?php
// Your code starts here.
if (!defined('ABSPATH')) {
    exit;
}

class Chatbot_Content_Generator {
    private $api_key_option;

    public function __construct() {
        // Set the API key option name dynamically based on the plugin's name or a unique identifier.
        $this->api_key_option = 'chatbot_content_generator_api_key';

        // Add hooks to handle the plugin's functionality.
        add_action('admin_menu', array($this, 'add_plugin_menu'));
        add_action('admin_post_generate_content', array($this, 'handle_content_generation'));

        // Add an action to register plugin settings.
        add_action('admin_init', array($this, 'register_settings'));
    }

    // Register plugin settings for the API key.
    public function register_settings() {
        register_setting('chatbot-content-generator-settings', $this->api_key_option);
    }

    // Add a menu item to the WordPress admin menu.
    public function add_plugin_menu() {
        // Main menu item
        add_menu_page(
            'Chatbot Content Generator',
            'Chatbot Generator',
            'manage_options', // Capability required to access the main menu
            'chatbot-content-generator',
            array($this, 'generate_content_page')
        );
    }

    // Callback function to display the content generation page.
    public function generate_content_page() {
        echo '<div class="wrap">';
        echo '<h2>Chatbot Content Generator</h2>';

        // Display a settings form for the API key.
        $this->display_api_key_settings();

        // Display the content generation form.
        $this->display_content_generation_form();

        echo '</div>';
    }

    // Display the API key settings form.
    public function display_api_key_settings() {
        ?>
<h3>ChatGPT API Key</h3>
<form method="post" action="options.php">
  <?php settings_fields('chatbot-content-generator-settings'); ?>
  <?php do_settings_sections('chatbot-content-generator-settings'); ?>
  <label for="<?php echo $this->api_key_option; ?>">API Key:</label>
  <input type="text" name="<?php echo $this->api_key_option; ?>" id="<?php echo $this->api_key_option; ?>"
    value="<?php echo esc_attr(get_option($this->api_key_option)); ?>">
  <?php submit_button('Save API Key'); ?>
</form>
<?php
    }

    // Display the content generation form.
    public function display_content_generation_form() {
        ?>
<form method="post" action="<?php echo admin_url('admin-post.php'); ?>">
  <input type="hidden" name="action" value="generate_content">
  <?php wp_nonce_field('generate_content', 'generate_content_nonce'); // Add a nonce field for security ?>
  <label for="title">Enter Title:</label>
  <input type="text" name="title" id="title" required>
  <input type="submit" value="Generate Content">
</form>
<?php
    }

    // Handle content generation when the form is submitted.
    public function handle_content_generation() {
        // Check if the user has the required capability.
        if (!current_user_can('manage_options')) {
            wp_die('You do not have permission to generate content.');
        }

        // Verify the nonce for security.
        if (!isset($_POST['generate_content_nonce']) || !wp_verify_nonce($_POST['generate_content_nonce'], 'generate_content')) {
            wp_die('Security check failed. Please try again.');
        }

        // Get the title from the form submission.
        $title = sanitize_text_field($_POST['title']);

        // Implement your chatbot-based content generation logic here.
        $generated_content = $this->generate_content($title);

        // Output the generated content.
        echo '<div class="wrap">';
        echo '<h2>Generated Content</h2>';
        echo '<p>' . esc_html($generated_content) . '</p>';
        echo '</div>';
    }

    // Implement content generation logic here (placeholder example).
    // Implement content generation logic here (with the model parameter).
public function generate_content($title) {
    $api_key = get_option($this->api_key_option); // Retrieve the API key from the settings.

    // Check if the API key is empty or invalid.
    if (empty($api_key)) {
        return "Please enter a valid ChatGPT API key in the plugin settings.";
    }

    // Define the API endpoint.
    $api_endpoint = 'https://api.openai.com/v1/completions';

    // Specify the GPT-3 model you want to use (e.g., 'davinci' or 'curie').
    $model = 'davinci'; // You can change this to 'curie' if needed.

    // Prepare the data for the API request, including the model parameter.
    $data = json_encode(array(
        'model' => $model,
        'prompt' => "Generate content for the title: $title",
        'max_tokens' => 200, // Adjust the max tokens as needed.
    ));

    // Log the API request data for debugging.
    error_log('API Request Data: ' . print_r($data, true));

    // Set up cURL options for the API request.
$ch = curl_init($api_endpoint);
curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, array(
    'Content-Type: application/json',
    'Authorization: Bearer ' . $api_key,
));

// Execute the API request.
$response = curl_exec($ch);

// Check for cURL errors.
if (curl_errno($ch)) {
    return "cURL Error: " . curl_error($ch);
}

$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

// Close the cURL handle.
curl_close($ch);


    // Log the API response for debugging.
    error_log('API Response: ' . $response);

    // Handle the response from the API.
    if ($http_code === 200) {
        $generated_content = json_decode($response, true);
        if (isset($generated_content['choices'][0]['text'])) {
            return $generated_content['choices'][0]['text'];
        } else {
            return "Error: No content generated.";
        }
    } else {
        return "Error: Unable to generate content. API request failed. Response: $response";
    }
}

}