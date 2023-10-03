<?php
/**
 * Plugin Name:     ChatGPT Content Generator Plugin
 * Plugin URI:      https://github.com/Rajan1308/chatgpt-content-generator
 * Description:     This plugin help to create content
 * Author:          Rajan Gupta
 * Author URI:      https://rajan1308.github.io/
 * Text Domain:     chatgpt-content-generator
 * Domain Path:     /languages
 * Version:         0.1.0
 *
 * @package         Chatgpt_Content_Generator
 */

// Your code starts here.
if (!defined('ABSPATH')) {
  exit;
}


// Include the main plugin class.
require_once(plugin_dir_path(__FILE__) . 'inc/class-chatbot-content-generator.php'); 
// Initialize the plugin.
$chatbot_content_generator = new Chatbot_Content_Generator();