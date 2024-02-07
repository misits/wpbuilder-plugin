/**
 * Handles UI interactions and CodeMirror initialization for the admin page.
 * Uses jQuery for event handling and CodeMirror for code editing.
 *
 * @class AdminPageHandler
 */
jQuery(document).ready(function ($) {
  // Icon select menu
  function updateIconPreview(iconValue) {
    $("#icon_preview").attr(
      "class",
      "wp-menu-image dashicons-before dashicons-icon-" + iconValue
    );
  }

  updateIconPreview($("#model_icon").val());

  // Event listener for select change
  $("#model_icon").change(function () {
    var selectedIcon = $(this).val();
    updateIconPreview(selectedIcon);
  });
  /**
   * Initializes CodeMirror for the textarea with the ID "file-content".
   */
  function initializeCodeMirror() {
    if (document.getElementById("file-content")) {
      let extension = $("#file-content").data("extension");

      switch (extension) {
        case "css":
          extension = "css";
          break;
        case "js":
          extension = "javascript";
          break;
        case "php":
          extension = "php";
          break;
        case "html":
          extension = "htmlmixed";
          break;
        default:
          extension = "htmlmixed";
          break;
      }

      let editor = CodeMirror.fromTextArea(
        document.getElementById("file-content"),
        {
          lineNumbers: true,
          matchBrackets: true,
          mode: extension,
          indentUnit: 4,
          indentWithTabs: true,
          enterMode: "keep",
          tabMode: "shift",
        }
      );
    }
  }

  // Initialize CodeMirror if not already initialized
  if (!window.codemirrorInitialized) {
    initializeCodeMirror();
    window.codemirrorInitialized = true;
  }

  /**
   * Handles switching between tabs and updating the URL hash.
   *
   * @param {string} tabId - The ID of the tab to switch to.
   */
  function handleTabSwitch(tabId) {
    // Hide all tab contents
    $(".tab-content").hide();

    $(".tab-content").css("opacity", "1");

    // Remove active class from all tabs
    $(".nav-tab").removeClass("nav-tab-active");

    // Show the selected tab content
    $(tabId).show();

    // Add active class to the clicked tab
    $('.nav-tab[href="' + tabId + '"]').addClass("nav-tab-active");
  }

  // Get initial tab ID from URL hash or default to "#tab1"
  let initialTabId = window.location.hash || "#tab1";
  handleTabSwitch(initialTabId);

  // Tab click event
  $(".nav-tab-wrapper a").on("click", function (e) {
    e.preventDefault();

    // Get the href attribute of the clicked tab
    let tabId = $(this).attr("href");

    // Update the URL hash
    window.location.hash = tabId;

    // Handle tab switching
    handleTabSwitch(tabId);

    // remove the success message
    $("#response-message").removeClass().html("");
  });

  // Make the list collapsible
  $(".collapsible-title").click(function () {
    $(this).next(".collapsible-list").toggleClass("collapsed");
  });
});
