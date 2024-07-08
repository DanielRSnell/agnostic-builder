<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Tailwind CSS with Dynamic Config and Plugins</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script type="module">
      // Make defaultTheme and colors available globally
      window.defaultTheme = tailwind.defaultTheme;
      window.colors = tailwind.colors;

      // Dynamic import function with logging
      async function dynamicImport(module) {
        try {
          const imported = await import(`https://cdn.skypack.dev/${module}`);
          console.log(`Successfully resolved plugin: ${module}`);
          return imported.default;
        } catch (error) {
          console.error(`Failed to resolve plugin: ${module}`, error);
          return null;
        }
      }

      // Function to transform require calls to dynamic imports
      function transformConfig(configString) {
        return configString.replace(
          /require\(['"](.*?)['"]\)/g,
          (match, p1) => `await dynamicImport('${p1}')`
        );
      }

      let lastSavedCSS = '';

      async function saveTailwindCSS(css) {
        if (css === lastSavedCSS) {
            return;
        }

        const data = new FormData();
        data.append('action', 'save_tailwind_css');
        data.append('nonce', '<?php echo wp_create_nonce('save_tailwind_css_nonce'); ?>');
        data.append('css', css);

        try {
            const response = await fetch('<?php echo admin_url('admin-ajax.php'); ?>', {
                method: 'POST',
                credentials: 'same-origin',
                body: data
            });
            const result = await response.json();
            if (result.success) {
                console.log('Tailwind CSS saved successfully');
                lastSavedCSS = css;

                // Post message to parent window
                window.parent.postMessage({ type: 'tailwindCSSSaved' }, '*');
            } else {
                console.error('Failed to save Tailwind CSS:', result.data);
            }
        } catch (error) {
            console.error('Error saving Tailwind CSS:', error);
        }
    }

      function isTailwindStyleTag(node) {
        return node.nodeName === 'STYLE' &&
               node.textContent.includes('/* ! tailwindcss v3.4.4 | MIT License | https://tailwindcss.com */');
      }

      function setupStyleObservation() {
        let tailwindStyleTag = null;

        const observer = new MutationObserver((mutations) => {
          let shouldCheckContent = false;

          for (const mutation of mutations) {
            if (mutation.type === 'childList') {
              const addedNodes = Array.from(mutation.addedNodes);
              const newTailwindTag = addedNodes.find(isTailwindStyleTag);
              if (newTailwindTag) {
                tailwindStyleTag = newTailwindTag;
                shouldCheckContent = true;
                break;
              }
            } else if (mutation.type === 'characterData' &&
                       mutation.target.nodeName === '#text' &&
                       isTailwindStyleTag(mutation.target.parentNode)) {
              tailwindStyleTag = mutation.target.parentNode;
              shouldCheckContent = true;
              break;
            }
          }

          if (shouldCheckContent && tailwindStyleTag) {
            const currentContent = tailwindStyleTag.textContent;
            if (currentContent !== lastSavedCSS) {
              console.log('Tailwind style content changed, saving...');
              saveTailwindCSS(currentContent);
            }
          }
        });

        observer.observe(document.head, {
          childList: true,
          characterData: true,
          subtree: true
        });

        console.log('Started observing head for Tailwind style changes');

        // Initial check for existing Tailwind style tag
        tailwindStyleTag = Array.from(document.head.getElementsByTagName('style')).find(isTailwindStyleTag);
        if (tailwindStyleTag) {
          console.log('Initial Tailwind style tag found, saving content');
          saveTailwindCSS(tailwindStyleTag.textContent);
        } else {
          console.log('No initial Tailwind style tag found');
        }

        // Set up an interval to check for content changes
        setInterval(() => {
          if (tailwindStyleTag) {
            const currentContent = tailwindStyleTag.textContent;
            if (currentContent !== lastSavedCSS) {
              console.log('Tailwind style content changed (interval check), saving...');
              saveTailwindCSS(currentContent);
            }
          }
        }, 1000); // Check every second
      }

      // Get and transform the user config
      const userConfigString = `<?php echo get_user_tailwind_config_string(); ?>`;
      console.log('User config string:', userConfigString);
      const transformedConfigString = transformConfig(userConfigString);
      console.log('Transformed config string:', transformedConfigString);

      // Create a new function to evaluate the transformed config
      const configFunction = new Function('colors', 'defaultTheme', 'dynamicImport', `
        return (async () => {
          const module = {};
          ${transformedConfigString}
          return module.exports;
        })();
      `);

      // Execute the config function and set up styles
      (async function() {
        try {
          console.log('Starting to resolve Tailwind configuration and plugins...');
          const config = await configFunction(colors, defaultTheme, dynamicImport);
          tailwind.config = config;
          console.log('Tailwind config successfully set:', tailwind.config);

          setupStyleObservation();

        } catch (error) {
          console.error('Error in Tailwind configuration:', error);
        }
      })();
    </script>
    <style type="text/tailwindcss">
      <?php echo get_user_tailwind_css_string(); ?>
    </style>
</head>
<body>
    <?php
function get_tailwind_bundle_markup()
{
    $all_content = '';

    // Pull templates from various sources
    $templates = array();

    // Pages
    $pages = get_pages();
    foreach ($pages as $page) {
        $templates[] = array('type' => 'page', 'content' => $page->post_content);
    }

    // Custom post types: lc_block, lc_section, lc_partial, lc_dynamic_sections
    $custom_post_types = array('lc_block', 'lc_section', 'lc_partial', 'lc_dynamic_sections');
    foreach ($custom_post_types as $post_type) {
        $posts = get_posts(array('post_type' => $post_type, 'numberposts' => -1));
        foreach ($posts as $post) {
            $templates[] = array('type' => $post_type, 'content' => $post->post_content);
        }
    }

    // Combine all templates
    foreach ($templates as $template) {
        $all_content .= '<div class="hidden template-' . esc_attr($template['type']) . '">';
        $all_content .= $template['content'];
        $all_content .= '</div>';
    }

    // Process all content at once
    $output = do_shortcode('[twig]' . $all_content . '[/twig]');

    return $output;
}

// Output the markup
echo get_tailwind_bundle_markup();
?>


        <?php echo_tailwind_autocomplete_scripts();?>

</body>
</html>