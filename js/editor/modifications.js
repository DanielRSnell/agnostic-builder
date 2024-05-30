window.global_autocompletes = [];
window.global_snippets = [];




// class ClassOrganizer {
//   constructor(selector) {
//     this.selector = 'main#lc-main';
//   }

//   organizeClasses() {
//     const srcHtml = getPageHTML(this.selector);
//     if (!srcHtml) {
//       console.error('Unable to get page HTML');
//       return;
//     }

//     // console.log('Organizer | Found HTML:', srcHtml);

//     const parser = new DOMParser();
//     const doc = parser.parseFromString(srcHtml, 'text/html');

//     doc.querySelectorAll('*').forEach(element => {
//       // Handle cases where className is an SVGAnimatedString or similar
//       const classList = typeof element.className === 'string' ? element.className : element.getAttribute('class');
//       if (!classList) return; // Skip elements without a class attribute

//       const originalClasses = classList.split(/\s+/);
//       const { responsiveClasses, otherClasses } = this.separateClasses(originalClasses);

//       // Sort non-responsive classes alphabetically, considering classes starting with a '-'
//       const sortedOtherClasses = otherClasses.sort((a, b) => {
//         const classA = a.startsWith('-') ? a.slice(1) : a;
//         const classB = b.startsWith('-') ? b.slice(1) : b;
//         return classA.localeCompare(classB);
//       });

//       // Organize responsive classes by a predefined order
//       const organizedResponsiveClasses = this.organizeResponsiveClasses(responsiveClasses);

//       // Combine and set the new class list
//       const newClassList = [...sortedOtherClasses, ...organizedResponsiveClasses].join(' ');
//       element.setAttribute('class', newClassList); // Use setAttribute to be consistent
//     });


//     // Update the page with the organized classes
//     setPageHTML(this.selector, doc.body.innerHTML);
//   }

//   separateClasses(classes) {
//     const responsiveClasses = [];
//     const otherClasses = [];

//     classes.forEach(className => {
//       if (className.includes(':')) {
//         responsiveClasses.push(className);
//       } else {
//         otherClasses.push(className);
//       }
//     });

//     return { responsiveClasses, otherClasses };
//   }

//   organizeResponsiveClasses(classes) {
//     const order = ['xs', 'sm', 'md', 'lg', 'xl', '2xl', '3xl', '4xl', '5xl'];

//     return classes.sort((a, b) => {
//       const prefixA = a.split(':')[0];
//       const prefixB = b.split(':')[0];
//       return order.indexOf(prefixA) - order.indexOf(prefixB);
//     });
//   }
// }

// class ContentEnhancer {
//   constructor(container) {
//     this.container = container;
//   }

//   enhanceContent() {
//     const htmlString = getPageHTML('main#lc-main');
//     if (!htmlString) {
//       console.error('Unable to get page HTML');
//       return;
//     }

//     // Parse the HTML string into a document fragment
//     const parser = new DOMParser();
//     const html = parser.parseFromString(htmlString, 'text/html');

//     // Found the HTML

//        // If there is a section with children that are sections, unwrap the parent section tag while preserving all children
//       html.querySelectorAll('section').forEach(section => {
//         const childSections = section.querySelectorAll('section');
//         if (childSections.length > 0) {
//           while (section.firstChild) {
//             section.parentNode.insertBefore(section.firstChild, section);
//           }
//           section.parentNode.removeChild(section);
//         }
//       });
      
//       // Remove all <br> tags
//       html.querySelectorAll('br').forEach(br => {
//         br.parentNode.removeChild(br);
//       });

//     this.wrapOrphanText(html.body, ['div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5']);

//     // Enhance divs with child elements, skipping aria-hidden and sr-only
//     html.querySelectorAll('div:not(.lc-block):not([aria-hidden]):not(.sr-only)').forEach(div => {
//       if (Array.from(div.childNodes).some(node => node.nodeType === Node.ELEMENT_NODE)) {
//         div.classList.add('lc-block');
//       }
//     });

//     // Enhance a tags, skipping aria-hidden and sr-only
//     html.querySelectorAll('a:not([lc-helper="button"]):not([aria-hidden]):not(.sr-only)').forEach(a => {
//       a.setAttribute('lc-helper', 'button');
//     });

//     // Enhance headers, spans, and paragraphs that don't contain children elements other than text, skipping aria-hidden and sr-only
//     html.querySelectorAll('header:not([aria-hidden]):not(.sr-only), span:not([aria-hidden]):not(.sr-only), h1:not([aria-hidden]):not(.sr-only), h2:not([aria-hidden]):not(.sr-only), h3:not([aria-hidden]):not(.sr-only), h4:not([aria-hidden]):not(.sr-only), h5:not([aria-hidden]):not(.sr-only), p:not([aria-hidden]):not(.sr-only)').forEach(element => {
//       if (this.shouldBeEditable(element)) {
//         element.setAttribute('editable', 'inline');
//       }
//     });

//     // Enhance svg elements, skipping aria-hidden and sr-only
//     html.querySelectorAll('svg:not([lc-helper="svg-icon"]):not([aria-hidden]):not(.sr-only)').forEach(svg => {
//       svg.setAttribute('lc-helper', 'svg-icon');
//     });

//     // Clear any empty <p> tags
//     html.querySelectorAll('p:empty').forEach(p => {
//       p.parentNode.removeChild(p);
//     });

//     // Preview the markup

//     // Update the page with the enhanced HTML
//     setPageHTML('main#lc-main', html.body.innerHTML);
//   }


//  wrapOrphanText(targetElement, selectors) {
//   selectors.forEach(selector => {
//     targetElement.querySelectorAll(selector).forEach(element => {
//       const childNodes = Array.from(element.childNodes);
//       const hasInnerText = childNodes.some(child =>
//         child.nodeType === Node.TEXT_NODE && child.textContent.trim().length > 0
//       );
//       const hasElementChild = childNodes.some(child => child.nodeType === Node.ELEMENT_NODE);

//       if (hasInnerText && hasElementChild) {
//         let textNodes = [];
//         childNodes.forEach(child => {
//           if (child.nodeType === Node.TEXT_NODE && child.textContent.trim().length > 0) {
//             // Check if the text node contains curly brackets
//             if (child.textContent.includes('{') || child.textContent.includes('}')) {
//               return; // Skip this orphan if it contains curly brackets
//             }
//             textNodes.push(child);
//           } else if (child.nodeType === Node.ELEMENT_NODE) {
//             if (textNodes.length > 0) {
//               const span = document.createElement('span');
//               textNodes.forEach(textNode => span.appendChild(textNode));
//               element.insertBefore(span, child);
//               textNodes = [];
//             }
//           }
//         });

//         if (textNodes.length > 0) {
//           const span = document.createElement('span');
//           textNodes.forEach(textNode => span.appendChild(textNode));
//           element.appendChild(span);
//         }
//       }
//     });
//   });
// }

//   shouldBeEditable(element) {
//     // Returns true if the element has no child elements other than text nodes
//     const childElements = Array.from(element.childNodes).filter(child => child.nodeType === Node.ELEMENT_NODE);
//     return childElements.length === 0;
//   }
// }


// class KeyboardAndClickHandler {
//   constructor() {
//     this.classOrganizer = new ClassOrganizer('main#lc-main');
//     this.contentEnhancer = new ContentEnhancer('main#lc-main');
//     this.initializeEventListeners();
//   }

//   initializeEventListeners() {
//     document.addEventListener('keydown', this.handleKeydownEvent.bind(this));
//   }

//   handleKeydownEvent(e) {
//     if ((e.ctrlKey || e.metaKey) && (e.key === 's' || e.key === 'e' || e.key === 'x')) {
//       e.preventDefault(); // Prevent default action for these shortcuts
//       this.executeSequence();
//     }
//   }

  

//   executeSequence() {
//     // Step 1: Close the editor
//     this.simulateClick('.lc-editor-close');

//     // Ensure actions are sequential and not simultaneous
//     setTimeout(() => {
//       // Organize classes and enhance content
//       // this.classOrganizer.organizeClasses();
//       this.contentEnhancer.enhanceContent();

//       // Step 2: Save the content after a slight delay to ensure class organization and content enhancement are processed
//       setTimeout(() => {
//         this.simulateClick('#main-save');

//         // Step 3: Re-open the editor after the save is completed
//         setTimeout(() => {
//           // this.simulateClick('.open-main-html-editor');
//         }, 10); // Adjust timing as needed
//       }, 10); // Adjust timing as needed
//     }, 10); // Adjust timing as needed
//   }

//   simulateClick(selector) {
//     const element = document.querySelector(selector);
//     if (element) {
//       element.click();
//     } else {
//       console.warn(`Element with selector ${selector} not found.`);
//     }
//   }
// }

// class IframeHandler {
//   constructor(iframeId, targetSelector) {
//     this.iframeId = iframeId;
//     this.targetSelector = targetSelector;
//     this.iframe = null;
//     this.iframeDoc = null;
//     this.container = null;
//   }

//   initialize() {
//     this.iframe = this.getIframe();
//     if (this.iframe) {
//       this.iframeDoc = this.getIframeDoc();
//       if (this.iframeDoc) {
//         this.container = this.findTargetElement();
//       }
//     }
//   }

//   getIframe() {
//     const iframe = document.getElementById(this.iframeId);
//     this.log('Frame Check | iframe', iframe ? iframe : 'Not found');
//     return iframe || null;
//   }

//   getIframeDoc() {
//     if (!this.iframe) {
//       this.log('Frame Check | iframeDoc', 'Iframe not available');
//       return null;
//     }
//     const doc = this.iframe.contentDocument || this.iframe.contentWindow.document;
//     this.log('Frame Check | iframeDoc', doc ? doc : 'Document not accessible');
//     return doc || null;
//   }

//   findTargetElement() {
//     if (!this.iframeDoc) {
//       this.log('Frame Check | container', 'Document not available');
//       return null;
//     }
//     const element = this.iframeDoc.querySelector(this.targetSelector);
//     this.log('Frame Check | container', element ? element : 'Target element not found');
//     return element || null;
//   }

//   getTargetElement() {
//     if (!this.container) {
//       this.initialize();
//     }
//     return this.container;
//   }

//   log(message, output) {
//   }
// }

// function setAndCheckDocGlobal() {
//   // Ensure setPageHTML and getPageHTML are globally accessible
//   if (typeof setPageHTML === 'function') {
//     window.setPageHTML = setPageHTML;
//   } else {
//     console.warn('`setPageHTML` is not defined or not a function.');
//   }

//   if (typeof getPageHTML === 'function') {
//     window.getPageHTML = getPageHTML;
//   } else {
//     console.warn('`getPageHTML` is not defined or not a function.');
//   }
// }

// window.addEventListener('load', () => {
//   const iframeHandler = new IframeHandler('previewiframe', 'main#lc-main');

//   iframeHandler.initialize();
//   window.iframeHandler = iframeHandler;
  
//   // window.keyboardAndClickHandler = new KeyboardAndClickHandler();
//   // window.organizer = new ClassOrganizer('main#lc-main');
//   window.enhancer = new ContentEnhancer('main#lc-main');

//   setAndCheckDocGlobal();
// });

// Check to see why it's not recognitizing the document.addEventListener

// Function to generate a unique selector for an element
function getSelector(element) {
  if (element.id) {
    return '#' + element.id;
  } else if (element.classList.length > 0) {
    return '.' + element.classList[0];
  } else {
    return element.tagName.toLowerCase();
  }
}

function removeBuilderClasses(classes) {
  const builderClasses = [
    'lc-block',
    'lc-block-inner',
    'lc-highlight-block',
    'lc-highlight-currently-editing',
    'lc-highlight-mainpart',
  ];
  // classes is a string with each class on a new line
  return classes.split('\n').filter(className => !builderClasses.includes(className)).join('\n');
}

function formatClasses(classes) {
  // Split classes into an array
  const classArray = classes.split(' ');

  // Group classes by prefix
  const groupedClasses = {
    'non-prefixed': []
  };
  classArray.forEach(className => {
    const parts = className.split(':');
    if (parts.length > 1) {
      const prefix = parts[0];
      if (!groupedClasses[prefix]) {
        groupedClasses[prefix] = [];
      }
      groupedClasses[prefix].push(className);
    } else {
      groupedClasses['non-prefixed'].push(className);
    }
  });

  // Join classes with spaces between prefixes
  let formattedClasses = groupedClasses['non-prefixed'].join('\n') + '\n\n';
  delete groupedClasses['non-prefixed'];

  Object.entries(groupedClasses).forEach(([prefix, group]) => {
    formattedClasses += group.join('\n') + '\n\n';
  });

  return removeBuilderClasses(formattedClasses.trim());
}

function organizeSiulaClasses(classes) {
  const frame = document.getElementById('previewiframe');
  // Ensure the frame exists
  if (!frame) {
    console.error('Preview iframe not found');
    return formatClasses(classes);
  }

  // Content Window
  const cw = frame.contentWindow;
  if (!cw || !cw.siul || !cw.siul.module) {
    console.error('siul module not found in Content Window');
    return formatClasses(classes);
  }

  const siulaClasses = formatClasses(cw.siul.module.classSorter.sort(classes));
  return siulaClasses;
}


function setManagerSession(targetElement, classes) {
  const el_selector = CSSelector(targetElement);
    // Update the active_selector and active_selector_classes in the parent window
    window.active_selector = el_selector;
    window.active_selector_classes = classes;

    // Check if the sidebar is relevant
    checkRelevantSidebar(el_selector);

    const organize = organizeSiulaClasses(classes.join(' '));
    
    // Update Selection Menu
    lc_tweak_editor.session.setValue(organize);

    // add data-active-item attribute to the clicked item
    targetElement.setAttribute('data-active-item', el_selector);
}

function checkRelevantSidebar(selector) {
  // get the document's #sidepanel then loop over its children to check if any of them don't contain style="display: none;"
  const $sidepanel = $('#sidepanel');
  if (!$sidepanel.length) {
    return;
  }

  let sidebarOpen = false;

  $sidepanel.children().each(function() {
    if (!$(this).attr('style') || !$(this).attr('style').includes('display: none;')) {
      // if it does, check [selector] attribute to see if it matches the targetEl selector
      if ($(this).attr('selector') === selector) {
        // if it does, leave sidebar open
        sidebarOpen = true;
        return false; // break out of the loop
      }
    }
  });

  // otherwise $(".close-sidepanel").click();
  if (!sidebarOpen) {
    $('.close-sidepanel').click();
  }
}

window.active_multiselect = [];

// Function to attach the event listener to the previewiframe
function attachIframeClickListener(iframe) {
  iframe.contentDocument.addEventListener('click', function(event) {
    const targetElement = event.target;
    const selector = getSelector(targetElement);
    const classes = Array.from(targetElement.classList);
    
    const check = targetElement.closest('main#lc-main');
   
    
    if (check) {
      const isDebugManager = targetElement.closest('#debug-manager') !== null;
      
      if (!isDebugManager && (!classes.includes('live-shortcode') || targetElement.getAttribute('lc-helper') === 'posts-loop' || !classes.includes('lc-rendered-shortcode-wrap'))) {
        if (event.metaKey || event.ctrlKey) {
          // user-select none to main#lc-main
          console.log('Shift Click Event:', selector);
          window.active_multiselect.push(CSSelector(targetElement));
          console.log('Active Multiselect:', window.active_multiselect);
          
          if (targetElement.hasAttribute('data-active-item')) {
            targetElement.removeAttribute('data-active-item');
            // remove user-select none to main#lc-main
          } else {
            // remove all 
            targetElement.setAttribute('data-active-item', CSSelector(selector));
            targetElement.removeAttribute('data-hover-item');
            // remove user-select none to main#lc-main
          }
        } else {
          // remove all previous active items
          iframe.contentDocument.querySelectorAll('[data-active-item]').forEach(item => {
            item.removeAttribute('data-active-item');
          });
           window.tweaks.restore();
          console.log('Click Event:', selector);
          window.active_multiselect = [CSSelector(targetElement)];
          setManagerSession(targetElement, classes);
        }
      }
    }
  });

  // iframe.contentDocument.addEventListener('mouseover', function(event) {
  //   const targetElement = event.target;
  //   const selector = getSelector(targetElement);
  //   const classes = Array.from(targetElement.classList);

  //   const check = targetElement.closest('main#lc-main');

  //   if (check) {
  //     const isDebugManager = targetElement.closest('#debug-manager') !== null;
      
  //     if (!isDebugManager && (!classes.includes('live-shortcode') || targetElement.getAttribute('lc-helper') === 'posts-loop' || !classes.includes('lc-rendered-shortcode-wrap'))) {
  //       window.parent.hover_selector = selector;
  //       window.parent.hover_selector_classes = classes;

  //       if (!targetElement.hasAttribute('data-active-item')) {
  //         // targetElement.setAttribute('data-hover-item', selector);
  //       }
        
  //       // targetElement.removeAttribute('editable');

  //       if (targetElement.getAttribute('lc-helper') !== 'posts-loop' || !classes.includes('lc-rendered-shortcode-wrap') || !classes.includes('live-shortcode')) {
  //         targetElement.removeAttribute('lc-helper');
  //       }
  //     }
  //   }
  // });

  // iframe.contentDocument.addEventListener('mouseout', function(event) {
  //   const targetElement = event.target;
  //   const classes = Array.from(targetElement.classList);

  //   const check = targetElement.closest('main#lc-main');

  //   if (check) {
  //     const isDebugManager = targetElement.closest('#debug-manager') !== null;
      
  //     if (!isDebugManager && (targetElement.getAttribute('lc-helper') !== 'posts-loop' || !classes.includes('lc-rendered-shortcode-wrap') || !classes.includes('live-shortcode'))) {
  //       const previouslyHoveredItem = iframe.contentDocument.querySelector('[data-hover-item]');
  //       if (previouslyHoveredItem) {
  //         previouslyHoveredItem.removeAttribute('data-hover-item');
  //       }
  //     }
  //   }
  // });
}

// // Function to inject the CSS into the iframe's head
function injectCSS(iframe) {
  const css = `

lc-dynamic-twig {
  display: block!important;
} 

main [data-active-item]:not(:empty):not(image):not(path),
main [data-hover-item]:not(:empty):not(image):not(path) {
  position: relative;
  transition: all 0.3s ease-in-out;
}

main [data-active-item]:not(:empty):is(image):hover {

}

/* Hover and Active State for Items */
main [data-hover-item]:not(:empty),
main [data-active-item]:not(:empty):hover {
  cursor: crosshair !important;
}


main [data-active-item]:not(:empty)::before,
main [data-hover-item]:not(:empty)::before {
  content: '';
  position: absolute;
  top: -4px;
  left: -4px;
  right: -4px;
  bottom: -4px;
  border: 2px solid #f472b6;
  border-radius: 4px;
  opacity: 0;
  transition: opacity 0.3s ease-in-out, border-color 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
  pointer-events: none;
  z-index: 9999;
}

main [data-active-item]:not(:empty)::before {
  opacity: 1;
  border-color: #f472b6;
  box-shadow: 0 0 8px rgba(244, 114, 182, 0.8);
}

main [data-hover-item]:not(:empty)::before,
main [data-active-item]:not(:empty):hover::before,
main [data-hover-item]:not(:empty):hover::before {
  opacity: 0.8;
  border-color: #fb923c;
  box-shadow: 0 0 8px rgba(251, 146, 60, 0.8);
}

/* Additional styles for empty items */
main [data-active-item]:empty:before,
main [data-hover-item]:empty:before {
  opacity: 1;
  border-color: #22c55e;
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
}

main [data-hover-item]:empty:hover::before {
  opacity: 1;
  border-color: #22c55e;
  box-shadow: 0 0 8px rgba(34, 197, 94, 0.8);
}

/* Empty LC Blocks */

.lc-block:empty {
    display: block; 
    background: linear-gradient(135deg, #f0f0f0 25%, transparent 25%, transparent 50%, #f0f0f0 50%, #f0f0f0 75%, transparent 75%, transparent);
    background-size: 10px 10px;
    border: 1px solid #eaeaea;
}

.lc-block:empty:before {
    display: block;
    content: "Choose Block";
    text-align: center;
    margin: 20px 0;
    font-size: 11px;
    text-transform: uppercase;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    color: #666;
}

.lc-block:empty:hover {
    cursor: pointer;
    border-color: #0070f3;
}

.lc-block:empty:after {
    font-size: 14px;
    text-decoration: none;
    color: #999;
}

/* Empty Main Sections */

main > section:empty {
    display: block; 
    color: #666;
    background: linear-gradient(135deg, #fafafa 25%, transparent 25%, transparent 50%, #fafafa 50%, #fafafa 75%, transparent 75%, transparent);
    background-size: 20px 20px;
    border: 1px dashed #eaeaea;
}

main > section:empty:before {
    display: block;
    content: "A New Dummy Section";
    font-size: 15px;
    text-align: center;
    margin-top: 40px;
    text-transform: uppercase;
    font-weight: 500;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    color: #666;
}

main > section:empty:after {
    display: block;
    content: "Replace me with your choice!";
    text-decoration: none;
    margin-bottom: 40px;
    text-align: center;
    font-size: 11px;
    text-transform: uppercase;
    font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, 'Open Sans', 'Helvetica Neue', sans-serif;
    opacity: 0.5;
}

main > section:empty:hover {
    cursor: pointer;
    border-color: #0070f3;
}

  `;

  const style = iframe.contentDocument.createElement('style');
  style.textContent = css;
  iframe.contentDocument.head.appendChild(style);

  // const cssURL = '/wp-content/uploads/yabe-siul/cache/tailwind.css';

  // // Create a <link> element
  // const link = iframe.contentDocument.createElement('link');
  // link.rel = 'stylesheet';
  // link.href = cssURL;
  // iframe.contentDocument.head.appendChild(link);

}

// function passIframeScripts(iframe) {
//   const iframeWindow = iframe.contentWindow;
//   iframeWindow.ace = ace;
// }

function waitForIframeLoad() {
  const iframe = document.querySelector('#previewiframe');
  if (iframe) {
    iframe.addEventListener('load', function() {
      attachIframeClickListener(iframe);
      injectCSS(iframe);
      processTwigElements(iframe);
      // Colored Console Log to indicate the iframe is loaded
      console.log('%cIframe Loaded', 'color: #fff; background: #333; padding: 4px 8px; border-radius: 4px;');
      checkQueries();
      monitorIframeChanges(iframe);
      // Update LC Editor Options
      updatePluginOptions();
      // Pass Ace Editor to the iframe
      // passIframeScripts(iframe);
    });
  } else {
    // If the iframe is not found, wait for a short delay and check again
    setTimeout(waitForIframeLoad, 100);
  }
}

// Call the function to wait for the iframe to load
waitForIframeLoad();

// async function getTwigSnippets(manager) {
//   // fetch /wp-json/snippets/v1/twig

//     const response = await fetch('/wp-json/snippets/v1/twig', {
//       method: 'GET',
//       headers: {
//         'Content-Type': 'application/json',
//       },
//     });

//     const data = await response.json();
//     const completions = data.map(item => ({
//       name: item.prefix,
//       score: 100,
//       content: `${item.body}
//       \n {# ${item.description} #}`,
//     }));

//     manager.register(completions, 'twig');
//     localStorage.setItem('lc-twig-snippets', JSON.stringify(completions));
//   }

async function getACFsnippets(manager) {
  const request = await fetch('/wp-json/acf/v1/fields', {
    method: 'GET',
    headers: {
      'Content-Type': 'application/json',
    },
  });

  const completions = [];
  const data = await request.json();

  function generateCompletion(field, tier, fullPath) {
    const fieldName = field.name;
    const namePrefix = `code/acf/${fullPath}`;
    let completion = {};

    if (field.type === 'repeater') {
      const subCompletions = field.sub_fields.map((subField) => {
        const subFieldName = subField.name;
        const subFieldCompletion = generateCompletion(subField, tier === 'post' ? 'item' : 'nested', `${fullPath}/${subFieldName}`);
        return subFieldCompletion.content;
      }).join('\n');

      completion = {
        name: `${namePrefix}:repeater`,
        score: 100,
        content: `
{# Check if the repeater field "${fieldName}" has any data #}
{% if ${tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`} %}
  <div>
    {# Loop through each item in the repeater field #}
    {% for ${tier === 'post' ? 'item' : 'nested'} in ${tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`} %}
${subCompletions
  .split('\n')
  .map(line => `      ${line}`)
  .join('\n')}
      $0
    {% endfor %}
  </div>
{% endif %}
`,
      };
    } else if (field.type === 'flexible_content') {
      const subCompletions = field.layouts.map((layout) => {
        const layoutName = layout.name;
        const layoutSubFields = layout.sub_fields.map((subField) => {
          const subFieldName = subField.name;
          const subFieldCompletion = generateCompletion(subField, tier === 'post' ? 'item' : 'nested', `${fullPath}/${layoutName}/${subFieldName}`);
          return subFieldCompletion.content;
        }).join('\n');

        return `
{# Check if the current layout is "${layoutName}" #}
{% if ${tier === 'post' ? 'item' : 'nested'}.acf_fc_layout == '${layoutName}' %}
${layoutSubFields
  .split('\n')
  .map(line => `  ${line}`)
  .join('\n')}
  $0
{% endif %}
`;
      }).join('');

      completion = {
        name: `${namePrefix}:flexible_content`,
        score: 100,
        content: `
{# Check if the flexible content field "${fieldName}" has any data #}
{% if ${tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`} %}
  <div>
    {# Loop through each item in the flexible content field #}
    {% for ${tier === 'post' ? 'item' : 'nested'} in ${tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`} %}
${subCompletions
  .split('\n')
  .map(line => `      ${line}`)
  .join('\n')}
    {% endfor %}
  </div>
{% endif %}
`,
      };
    } else if (field.type === 'group') {
      const subCompletions = field.sub_fields.map((subField) => {
        const subFieldName = subField.name;
        const subFieldCompletion = generateCompletion(subField, fieldName, `${fullPath}/${subFieldName}`);
        return subFieldCompletion.content;
      }).join('\n');

      completion = {
        name: `${namePrefix}:group`,
        score: 100,
        content: `
{# Check if the group field "${fieldName}" has any data #}
{% if ${tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`} %}
  {# Set a variable for the group field data #}
  {% set ${fieldName} = ${tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`} %}
${subCompletions
  .split('\n')
  .map(line => `  ${line}`)
  .join('\n')}
  $0
{% endif %}
`,
      };
    } else if (field.type === 'image') {
      const fieldAccessor = tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`;
      completion = {
        name: `${namePrefix}:image`,
        score: 100,
        content: `
{# Check if the image field "${fieldName}" has any data #}
{% if ${fieldAccessor} %}
  {# Use TimberImage to get the optimized image URL #}
  <img src="{{ TimberImage(${fieldAccessor}).src }}">
  $0
{% endif %}
`,
      };
    } else if (field.type === 'gallery') {
      completion = {
        name: `${namePrefix}:gallery`,
        score: 100,
        content: `
{# Check if the gallery field "${fieldName}" has any data #}
{% if ${tier}.${fieldName} %}
  <div>
    {# Loop through each image in the gallery field #}
    {% for item in ${tier}.${fieldName} %}
      {# Use TimberImage to get the optimized image URL #}
      <img src="{{ TimberImage(item).src }}">
    {% endfor %}
  </div>
  $0
{% endif %}
`,
      };
    } else {
      const fieldAccessor = tier === 'post' ? `${tier}.meta('${fieldName}')` : `${tier}.${fieldName}`;
      completion = {
        name: `${namePrefix}:field`,
        score: 100,
        content: `
{# Check if the field "${fieldName}" has any data #}
{% if ${fieldAccessor} %}
  {# Output the field value #}
  {{ ${fieldAccessor} }}
  $0
{% endif %}
`,
      };
    }

    return completion;
  }

  function registerField(field, tier = 'post', parentPath = '') {
    const fieldName = field.name;
    const fullPath = parentPath ? `${parentPath}/${fieldName}` : fieldName;
    completions.push(generateCompletion(field, tier, fullPath));

    if (field.sub_fields) {
      field.sub_fields.forEach((subField) => {
        const subTier = field.type === 'repeater' ? (tier === 'post' ? 'item' : 'nested') : tier;
        registerField(subField, subTier, fullPath);
      });
    }
  }

  data.forEach((field) => registerField(field, 'post'));
  manager.register(completions, 'twig');
}


function updatePluginOptions() {
  window.lc_html_editor = ace.edit('lc-html-editor');
  window.lc_css_editor = ace.edit('lc-css-editor');

  // Update HTML to TWIG
  lc_html_editor.session.setMode('ace/mode/twig');

  // Update to monokai theme
  lc_html_editor.setTheme('ace/theme/tomorrow_night_bright');

  // Enable snippets
  lc_html_editor.setOptions({
    enableBasicAutocompletion: true,
    enableSnippets: true,
    enableLiveAutocompletion: true,
  });

  // Enable Twig Snippets
  const manager = ace.require('ace/snippets').snippetManager;
  // getTwigSnippets(manager);
  getACFsnippets(manager);
}


function monitorIframeChanges(iframe) {
  const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

  // Create a MutationObserver to watch for changes in the iframe's DOM
  const observer = new MutationObserver((mutationsList, observer) => {
    // Run checkQueries whenever a change is detected
    checkQueries();
  });

  // Configuration of the observer
  const config = {
    childList: true, // Watch for additions or removals of child nodes
    subtree: true    // Watch the entire subtree
  };
  console.log('iframe change');
  // Start observing the target node (iframe's body)
  observer.observe(iframeDocument.body, config);
}

function checkQueries() {
  // Use doc.querySelector to find all [lc-helper="posts-loop"] elements
  const postsLoops = doc.querySelectorAll('[lc-helper="posts-loop"]');
  console.log('Posts Loops:', postsLoops.length);

  // Get or create the container in the parent document
  let container = document.getElementById('query-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'query-container';
    // Create the header
    const header = document.createElement('div');
    header.id = 'query-header';
    header.textContent = 'Page Queries';
    container.appendChild(header);
    // Create the items container
    const itemsContainer = document.createElement('div');
    itemsContainer.id = 'query-items';
    container.appendChild(itemsContainer);
    document.body.appendChild(container);
  }


  // Get the query items container
  const itemsContainer = document.getElementById('query-items');

  // Clear the current queries
  itemsContainer.innerHTML = '';

  // Check if there are any queries
  if (postsLoops.length > 0) {
    // Show the container
    container.style.display = 'block';

    // Add new queries
    for (let i = 0; i < postsLoops.length; i++) {
      // Create a div element
      const div = document.createElement('div');
      // Extract output_dynamic_view_id from the shortcode
      const outputDynamicViewId = extractOutputDynamicViewId(postsLoops[i].innerHTML);
      // Format the output_dynamic_view_id or fallback to "Query: index"
      const displayName = outputDynamicViewId ? formatDisplayName(outputDynamicViewId) : `Query: ${i}`;
      // Set the text content
      div.textContent = displayName;

      // push to global autocompletes
      addCompletion(displayName, 'query');

      // Append the div to the items container
      itemsContainer.appendChild(div);
      // Set the data-query attribute to the index
      div.setAttribute('data-query', i);
      const selector = CSSelector(postsLoops[i]);
      div.setAttribute('data-query-selector', selector);
    }
  } else {
    // Hide the container
    container.style.display = 'none';
  }

  // Console log when complete
  console.log('Queries Checked');
}

function addCompletion(name, type) {

  switch (type) {
    case 'query':
      let field_name = name.split(' ').join('_').toLowerCase();
      console.log('Adding Completion:', name, type);
      let snippet = {
        name: `code/${type}/` + field_name,
        score: 100,
        content: `
          <div>
          {# This is a query for ${field_name} #}
            {% for item in ${field_name} %}
              <div>
                {{ item.title }}
              </div>
            {% endfor %}
          </div>`
      }

      let completion = {
        caption: `${type}/${field_name}`,
        score: 100,
        value: field_name,
        meta: 'query',
      }

      // Push to global completions and snippets
      window.global_autocompletes.push(completion);
      const manager = ace.require('ace/snippets').snippetManager;

      manager.register([snippet], 'twig');
      break;
    }

}

function extractOutputDynamicViewId(content) {
  const match = content.match(/output_dynamic_view_id\s*=\s*"([^"]+)"/);
  return match ? match[1] : null;
}

function formatDisplayName(name) {
  return name
    .split('_')
    .map(word => word.charAt(0).toUpperCase() + word.slice(1))
    .join(' ');
}


// Event listener for clicks on query-container > div
$(document).on('click', '#query-items > div', function() {
  const selector = $(this).attr('data-query-selector');
  const iframe = document.querySelector('#previewiframe');
  const iframeDocument = iframe.contentDocument || iframe.contentWindow.document;

  // Find and click the lc-helper element within the iframe
  const targetElement = iframeDocument.querySelector(selector);
  if (targetElement) {
    $(targetElement).trigger('click');
  } else {
    console.log(`Element with selector ${selector} not found in iframe`);
  }
});


function processTwigElements() {
    const iframe = document.getElementById('previewiframe');
    // Get all lc-dynamic-twig elements within the iframe
    // send for twig processing
    render_dynamic_templating_twig();
}

function render_dynamic_templating_twig() {
  const iframe = document.getElementById('previewiframe');
  if (iframe && iframe.contentDocument) {
    const twigDocument = iframe.contentDocument;
    const element = twigDocument.querySelector('main#lc-main');
    const urlParams = new URLSearchParams(window.location.search);
    if (element && element.innerHTML) {
      const trueElement = doc.querySelector('main#lc-main');
      if (!trueElement) {
        console.log('No main#lc-main element found in the parent document');
        return;
      }
      // Check if startViewTransition is available
      if (typeof twigDocument.startViewTransition === 'function') {
        const transition = twigDocument.startViewTransition(() => {
          const formData = new FormData();
          formData.append('action', 'lc_process_dynamic_templating_twig');
          formData.append('shortcode', trueElement.innerHTML);
          formData.append('post_id', lc_editor_current_post_id);
          formData.append('demo_id', (urlParams.get('demo_id') ?? false));

          return fetch(lc_editor_saving_url, {
            method: 'POST',
            body: formData
          })
          .then(response => response.text())
          .then(response => {
            // Update the main element with the response
            twigDocument.querySelector('main#lc-main').innerHTML = response;

            setTimeout(() => {
              // This keeps inspector in sync with editor changes
              updateInspectorData();

            }, 10);
          })
          .catch(error => {
            console.error('Error:', error);
          });
        });

        // Ensure transition is finished
        if (transition && typeof transition.finish === 'function') {
          transition.finish();
        } else {
          console.log('Transition object does not have a finish method');
        }
      } else {
        console.log('startViewTransition is not a function');
      }
    } else {
      console.log('No Twig Elements Found', element);
    }
  } else {
    console.log('Failed to get iframe contentDocument');
  }
}


document.addEventListener('lcUpdatePreview', function(event) {
  console.log('lcUpdatePreview event fired')
  const iframe = document.getElementById('previewiframe');
  const twigElements = event.detail.twigElements; // Assuming the twigElements are passed in the event detail
  processTwigElements();
  checkQueries();
});

// Open the HTML editor
 function openPicoHTMLEditor() {
            openHtmlEditor(active_selector);
          
            // click lc-editor-slide using vanilla JS
            setTimeout(() => {
            document.querySelector('.lc-editor-slide').click();
            }, 10);
        }

function sortPicoClasses() {
            var classes = lc_tweak_editor.getValue();
            classes.split('\n').join(' ');

            const organize = organizeSiulaClasses(classes);

            // Update the editor
            lc_tweak_editor.setValue(organize);
        }

// Listen to the document for whenever selector= attribute changes and console.log the changes
// Create a named function for the observer callback
function selectorAttributeObserverCallback(mutations) {
  mutations.forEach((mutation) => {
    if (mutation.type === 'attributes' && mutation.attributeName === 'selector') {
      const changedElement = mutation.target;
      const elementId = changedElement.id ? `ID: ${changedElement.id}` : 'No ID';
      const elementClasses = changedElement.classList.length > 0 ? `Classes: ${Array.from(changedElement.classList).join(', ')}` : 'No Classes';
      // do not select live-shortcode elements or lc-helper="posts-loop"
      if (changedElement.classList.contains('live-shortcode') || changedElement.getAttribute('lc-helper') === 'posts-loop') {
      console.log(`Selector attribute changed:`);
      console.log(`  Element: ${changedElement.tagName}`);
      console.log(`  Item: ${changedElement.getAttribute('item-type')}`);
      console.log(`  ${elementId}`);
      console.log(`  ${elementClasses}`);
      console.log(`  Selector: ${changedElement.getAttribute('selector')}`);

      // Get the iframe of the selector
      const previewEl = doc.querySelector(changedElement.getAttribute('selector'));
      // Get the classes of the selector
      const classes = Array.from(previewEl.classList);
      // Set manager session
      
      console.log('Preview Element:', previewEl);

      setManagerSession(previewEl, classes);
      }
    }
  });
}

// Create a MutationObserver instance with a unique name
const selectorAttributeObserver = new MutationObserver(selectorAttributeObserverCallback);

// Configure the observer options
const observerOptions = {
  attributes: true, // Listen for attribute changes
  attributeFilter: ['selector'], // Only observe changes to the 'selector' attribute
  subtree: true // Observe changes in the entire document subtree
};

// Start observing the document with the specified options
selectorAttributeObserver.observe(document, observerOptions);


function attachTreeViewItemListener() {
  $('#tree-body').on('click', '.tree-view-item', function(event) {
    if ($(event.target).closest('.tree-view-item').is(this)) {
      var selectorValue = $(this).attr('data-selector');

      // Set data-active-item attribute to the clicked item TODO: Add a check to see if the selector exists
      // $(this).attr('data-active-item', selectorValue);

      // Get classes of the selector
      const classes = Array.from(doc.querySelector(selectorValue).classList);
      const targetEl = doc.querySelector(selectorValue);
      console.log('TREE VIEW ITEM SELECTED:', selectorValue, classes);
      setManagerSession(targetEl, classes);
      window.tweaks.restore();
    }
  });
}

function detachTreeViewItemListener() {
  $('#tree-body').off('click', '.tree-view-item');
}

// Attach the listener when #tree-body is inserted into the DOM
$(document).on('DOMNodeInserted', '#tree-body', function() {
  attachTreeViewItemListener();
});

// // CSS styles for empty elements
var emptyDivStyles = `
[data-canvas-block]:empty:not([class]) {
  /* Styles for empty elements with [data-canvas-block] attribute and no classes */
  background-color: #f0f0f0;
  border: 1px dashed #999;
  min-height: 50px;
}

div:empty:not([class]):not([id]) {
  display: block;
  background: repeating-linear-gradient(-45deg, #ffe6ff, #e1ffff 5px, white 5px);
  border: 1px solid #333;
}

div:empty:not([class]):not([id]):before {
  display: block;
  content: "DIV";
  text-align: center;
  margin: 20px 0px;
  font-size: 11px;
  text-transform: uppercase;
  font-family: Arial;
  color: #333;
}

div:empty:not([class]):not([id]):hover {
  cursor: pointer;
}

div:empty:not([class]):not([id]) {
  border: 1px dashed #333;
}

div:empty:not([class]):not([id]):after {
  font-size: 14px;
  text-decoration: none;
  color: #999;
}

.container:empty {
  display: block;
  background: repeating-linear-gradient(-45deg, #ffe6ff, #e1ffff 5px, white 5px);
  border: 1px solid #333;
}

.container:empty:before {
  display: block;
  content: "CONTAINER";
  text-align: center;
  margin: 20px 0px;
  font-size: 11px;
  text-transform: uppercase;
  font-family: Arial;
  color: #333;
}

.container:empty:hover {
  cursor: pointer;
}

.container:empty {
  border: 1px dashed #333;
}

.container:empty:after {
  font-size: 14px;
  text-decoration: none;
  color: #999;
}

a[href="#"]:empty:not([class]):not([id]) {
  display: inline-block;
  background: repeating-linear-gradient(-45deg, #ffe6ff, #e1ffff 5px, white 5px);
  border: 1px solid #333;
  text-decoration: none;
}

a[href="#"]:empty:not([class]):not([id]):before {
  display: inline-block;
  content: "LINK";
  text-align: center;
  margin: 5px;
  font-size: 11px;
  text-transform: uppercase;
  font-family: Arial;
  color: #333;
}

a[href="#"]:empty:not([class]):not([id]):hover {
  cursor: pointer;
}

a[href="#"]:empty:not([class]):not([id]) {
  border: 1px dashed #333;
}

a[href="#"]:empty:not([class]):not([id]):after {
  font-size: 14px;
  text-decoration: none;
  color: #999;
}
  
`;

// Function to inject CSS styles into the iframe
function injectEmptyDivStyles() {
  var iframe = document.getElementById('previewiframe');
  var iframeDoc = iframe.contentDocument || iframe.contentWindow.document;
  var styleElement = iframeDoc.createElement('style');
  styleElement.textContent = emptyDivStyles;
  iframeDoc.head.appendChild(styleElement);
}

// Call the function to inject styles when the iframe is loaded
document.getElementById('previewiframe').addEventListener('load', injectEmptyDivStyles);


// Function to handle changes to the selector attribute
function handleSelectorChange(mutations) {
  mutations.forEach((mutation) => {
    if (mutation.type === 'attributes' && mutation.attributeName === 'selector') {
      const changedElement = mutation.target;
      
      const elementId = changedElement.id ? `ID: ${changedElement.id}` : 'No ID';
      const elementClasses = changedElement.classList.length > 0 ? `Classes: ${Array.from(changedElement.classList).join(', ')}` : 'No Classes';
      
      console.log(`Selector attribute changed:`);
      console.log(` Element: ${changedElement.tagName}`);
      console.log(` Item: ${changedElement.getAttribute('item-type')}`);
      console.log(` ${elementId}`);
      console.log(` ${elementClasses}`);
      console.log(` Selector: ${changedElement.getAttribute('selector')}`);
      
      // Get the iframe of the selector
      const iframe = document.querySelector('#previewiframe');
      const previewEl = iframe.contentDocument.querySelector(changedElement.getAttribute('selector'));
      
      // Get the classes of the selector
      const classes = Array.from(previewEl.classList);
      
      // Set manager session
      setManagerSession(previewEl, classes);
    }
  });
}

// Wait for the DOM to be fully loaded
document.addEventListener('DOMContentLoaded', () => {
  // Create a MutationObserver instance
  const observer = new MutationObserver(handleSelectorChange);

  // Configure the observer options
  const observerOptions = {
    attributes: true, // Listen for attribute changes
    attributeFilter: ['selector'], // Only observe changes to the 'selector' attribute
    subtree: true // Observe changes in the entire subtree of #sidepanel
  };

  // Get the #sidepanel element
  const sidepanel = document.querySelector('#sidepanel');

  // Start observing the #sidepanel element with the specified options
  observer.observe(sidepanel, observerOptions);
});