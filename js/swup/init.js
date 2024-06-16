

  document.addEventListener('DOMContentLoaded', function() {
   const swup = new Swup({
    containers: ['#theme-main'],
    native: true,
  });

  // Make swup a global variable
  window.swup = swup;

    // Reinitialize Alpine.js after Swup content is replaced
    swup.hooks.on('page:view', function() {
      if (window.Alpine) {
        window.Alpine.start();
      }
      initializeScripts();
      initializeFormSubmission();
    });
    
  });


function initializeScripts() {
  // any scripts you want to run on every page load or after swup replaces the content
}

initializeScripts()