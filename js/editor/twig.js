const timberstrap = true;
    
function filterPreviewHTML(input) {
    // Fix stretched links
    input = input.replace(/stretched-link/g, "");
   

    if (lc_editor_post_type == "lc_dynamic_template") {
        console.log('Dynamic template');
        // Wrap lc_ shortcodes
        input = input.replace(/\[lc_([^\]]+)\]/g, "<lc-dynamic-element hidden>[$&");
        input = input.replace(/\[\/lc_([^\]]+)\]/g, "$&</lc-dynamic-element>");

        // Wrap [twig] shortcode block
        input = input.replace(/\[twig\]([\s\S]*?)\[\/twig\]/g, "<lc-dynamic-twig hidden>[twig]$1[/twig]</lc-dynamic-twig>");
        checkQueries()
    } else {
        console.log('Not a dynamic template');
        //   input = input.replace(/\[lc_([^\]]+)\]/g, "<lc-dynamic-element hidden>[$&");
        // input = input.replace(/\[\/lc_([^\]]+)\]/g, "$&</lc-dynamic-element>");

        // // Wrap [twig] shortcode block
        // input = input.replace(/\[twig\]([\s\S]*?)\[\/twig\]/g, "<lc-dynamic-twig>[twig]$1[/twig]</lc-dynamic-twig>");
        checkQueries()
        processTwigElements(input);
        
    }
    return input;
}

function render_dynamic_content(selector){
	if (lc_editor_post_type == "lc_dynamic_template") {
        console.log('Dynamic Rendering')
		render_dynamic_templating_shortcodes_in(selector);
        // render_dynamic_templating_twig(selector);
        // updatePreviewSectorial('main#lc-main');

        
    } else {
        console.log('Broke Dynamic Rendering Twig')
		render_shortcodes_in(selector);
        // render_dynamic_templating_twig(selector);
        // updatePreviewSectorial('main#lc-main');
    }
}

// $(document).on('change', '#previewiframe', function(){
//     updatePreview();
// });

// In the parent window
// var iframe = document.getElementById('previewiframe');
// iframe.contentWindow.initializeDebugger(ace);

// document.addEventListener('DOMContentLoaded', function() {
//   // Get the current URL
//   const url = new URL(window.location.href);

//   // Get the existing URL parameters
//   const params = new URLSearchParams(url.search);

//   // Set the value of the demo_id parameter
//   params.set('demo_id', '259');

//   // Update the URL with the modified parameters
//   url.search = params.toString();

//   // Replace the current URL in the browser's address bar
//   window.history.replaceState({}, '', url);
// });