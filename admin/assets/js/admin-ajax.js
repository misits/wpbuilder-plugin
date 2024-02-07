/**
 * Handles AJAX requests for letious admin actions related to custom post types.
 * Uses jQuery for event handling and AJAX calls.
 *
 * @class CptwpAdmin
 */
jQuery(document).ready(function ($) {
  console.log("cptwp-admin.js loaded");
  /**
   * Handles the click event for the "Create Post Type" button.
   */
  $("#create-model-form").submit(function (e) {
    e.preventDefault();

    $.ajax({
      type: "post",
      url: ajaxurl, // WordPress AJAX URL
      data: {
        action: "create_cpt_models", // AJAX action name
        security: $("#create_model_nonce").val(), // Nonce for security
        formData: $("#create-model-form").serializeArray(), // Use serializeArray() to serialize form data
      },
      success: function (response) {
        // Handle the AJAX response
        $("#response-message").removeClass();
        $("#response-message")
          .addClass("notice notice-success is-dismissible")
          .html(response);

          // reload after 2 seconds
        setTimeout(function () {
          location.reload();
        }, 500);
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText); // Log the responseText for debugging
      },
    });
  });

  /**
   * Handles the click event for the "Create Block" button.
   */
  $("#create-block-form").submit(function (e) {
    e.preventDefault();

    $.ajax({
      type: "post",
      url: ajaxurl, // WordPress AJAX URL
      data: {
        action: "create_cpt_blocks", // AJAX action name
        security: $("#create_block_nonce").val(), // Nonce for security
        formData: $("#create-block-form").serializeArray(), // Use serializeArray() to serialize form data
      },
      success: function (response) {
        // Handle the AJAX response
        $("#response-message").removeClass();
        $("#response-message")
          .addClass("notice notice-success is-dismissible")
          .html(response);

          // reload after 2 seconds
        setTimeout(function () {
          location.reload();
        }, 500);
      },
      error: function (xhr, status, error) {
        console.log(xhr.responseText); // Log the responseText for debugging
      },
    });
  });
});
