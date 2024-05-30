

  document.addEventListener('DOMContentLoaded', function() {
   const swup = new Swup({
    containers: ['#theme-main'],
    native: true,
  });

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
  (function(d, t) {
    console.log('Initialize Chat');
    var v = d.createElement(t), s = d.getElementsByTagName(t)[0];
    v.onload = function() {
      window.voiceflow.chat.load({
        verify: { projectID: '65a6d31384ccee49f77b26b2' },
        url: 'https://general-runtime.voiceflow.com',
        versionID: 'production',
        render: {
          mode: 'embedded',
          target: document.getElementById('full-screen-chat')
        }
      });
    }
    v.src = "https://cdn.voiceflow.com/widget/bundle.mjs"; v.type = "text/javascript"; s.parentNode.insertBefore(v, s);
  })(document, 'script');

  function checkForShadowRoot() {
    const fullScreenChat = document.getElementById('full-screen-chat');
    if (fullScreenChat.shadowRoot) {
      console.log('Shadow root detected!');
      // Insert a <style> tag inside the shadow root
      const styleTag = document.createElement('style');
      fullScreenChat.shadowRoot.appendChild(styleTag);
      // Add your custom CSS here
      styleTag.textContent = `
        article > header {
          display: none!important;
        }
        footer {
          padding-bottom: 1rem!important;
        }
        aside {
          display: none!important;
        }
        div > article > main > div.vfrc-assistant-info.c-jJnANo {
          display: none!important;
        }
        .c-eGaVeY {
          padding-top: 60px;
          flex-grow: 0!important;
        }
      `;
      // Set initial styles for the parent element
      fullScreenChat.style.opacity = '0';
      fullScreenChat.style.transform = 'translateY(100%)';
      // Find the footer button and click it
      const footerButton = fullScreenChat.shadowRoot.querySelector('footer > button');
      if (footerButton) {
        footerButton.click();
        console.log('Footer button clicked!');
        // Animate the parent element using CSS transitions
        fullScreenChat.style.transition = 'opacity 1s ease-out, transform 1s ease-out';
        fullScreenChat.style.opacity = '1';
        fullScreenChat.style.transform = 'translateY(0)';
        setTimeout(function() {
          console.log('Parent element animation complete!');
        }, 1000);
      }
      else {
        console.log('Footer button not found.');
      }
    } else {
      console.log('Shadow root not found. Retrying in 100ms...');
      setTimeout(checkForShadowRoot, 100);
    }
  }

  checkForShadowRoot();
}

initializeScripts()