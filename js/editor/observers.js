class LayoutObserver {
  constructor() {
    this.observer = new MutationObserver(this.handleMutations.bind(this));
    this.editorWindow = document.getElementById("lc-html-editor-window");
    this.previewIframeWrap = document.getElementById("previewiframe-wrap");
    this.previewIframe = document.getElementById("previewiframe");
    this.sidePanel = document.getElementById("sidepanel");
    this.initObservers();
  }

  handleMutations(mutations) {
    mutations.forEach((mutation) => {
      if (mutation.target === this.editorWindow) {
        this.handleEditorWindowMutation(mutation);
      } else if (mutation.target === this.sidePanel) {
        this.handleSidePanelMutation(mutation);
      }
    });
  }

  handleEditorWindowMutation(mutation) {
    if (mutation.attributeName === "class") {
      const slider = document.querySelector(".lc-editor-window-sided");
      if (this.editorWindow.classList.contains("lc-editor-window-sided")) {
        this.activateSliderState(slider);
      } else {
        this.deactivateSliderState();
      }
    } else if (mutation.attributeName === "style") {
      this.adjustEditorWindowStyles();
    }
  }

  activateSliderState(slider) {
    if (slider) {
      this.setEditorWindowSliderStyle();
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.marginRight = "0";
      this.previewIframeWrap.style.width = "60%";
      // window.tweaks.minimize();
    } else {
      this.setEditorWindowSliderStyle();
      const previewWidth = parseFloat(this.previewIframe.style.width);
      this.adjustPreviewWindow(previewWidth);
    }
  }

  deactivateSliderState() {
    this.adjustSidePanelDisplay();
  }

setEditorWindowSliderStyle() {
  // Calculate the width of the editor window based on the difference between body width and #previewiframe width
  const previewIframe = document.getElementById("previewiframe");
  const previewIframeWidth = parseFloat(getComputedStyle(previewIframe).width);
  const bodyWidth = parseFloat(getComputedStyle(document.body).width);
  const editorWindowWidth = bodyWidth - (previewIframeWidth * 0.98);

  console.log('editorWindowWidth', `max-height: 100vh; display: block; height: calc(100% - var(--maintoolbar-height) - 0px) !important; width: ${editorWindowWidth}px !important; z-index: 999`);

  this.editorWindow.style.cssText = `
    max-height: 100vh;
    display: block;
    height: calc(100% - var(--maintoolbar-height) - 0px) !important;
    width: ${editorWindowWidth} !important;
    z-index: 999;
  `;
}

  adjustSidePanelDisplay() {
    const sidePanelStyle = getComputedStyle(this.sidePanel);

    if (sidePanelStyle.display !== "none") {
      this.previewIframeWrap.style.removeProperty("margin-left");
      this.previewIframeWrap.style.removeProperty("margin-right");
      this.previewIframeWrap.style.removeProperty("margin-top");
      this.previewIframeWrap.style.removeProperty("width");
      this.editorWindow.style.maxHeight = "100vh";
      // window.tweaks.move('81%', 45);
    } else {
      this.editorWindow.style.maxHeight = "100vh";
      this.editorWindow.style.removeProperty("height");
      this.editorWindow.style.removeProperty("width");
      this.previewIframeWrap.style.cssText = "";
      // window.tweaks.move(0, 45);
    }
  }

  hideElementGroup() {
    window.tweaks.minimize();
    window.inspector_data.minimize();
  }

  showElementGroup() {
    window.tweaks.restore() 
    window.inspector_data.restore()

  }

  adjustEditorWindowStyles() {
    const slider = document.querySelector(".lc-editor-window-sided");

    if (this.editorWindow.style.display === "none") {
      this.editorWindow.style.maxHeight = "100vh";
      this.editorWindow.style.removeProperty("height");
      this.editorWindow.style.removeProperty("width");
      this.editorWindow.classList.remove("lc-editor-window-sided");
      const winbox = document.getElementById('#winbox-1')
     this.showElementGroup()
    } else {

     this.hideElementGroup()
    
    }

    if (slider) {
      const mobilePreview = document.querySelector(".smartphone");

      if (!mobilePreview) {
        this.previewIframeWrap.style.width = "60%";
        this.previewIframeWrap.style.marginRight = "50";
      } else {
        const previewWidth = parseFloat(this.previewIframe.style.width);
        this.previewIframeWrap.style.removeProperty("width");
        this.adjustPreviewWindow(previewWidth);
      }
    } else {
      this.previewIframeWrap.style.removeProperty("margin-left");
      this.previewIframeWrap.style.removeProperty("margin-right");
      this.previewIframeWrap.style.removeProperty("width");
    }
  }

  handleSidePanelMutation(mutation) {
    if (mutation.attributeName === "style") {
      this.adjustSidePanelDisplay();
    }
  }

  adjustPreviewWindow(previewWidth) {
    if (previewWidth <= 360) {
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.marginRight = "18%";
      this.previewIframeWrap.style.marginTop = "1.5%";
    } else if (previewWidth <= 640) {
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.marginRight = "10%";
      this.previewIframeWrap.style.marginTop = "1.5%";
    } else if (previewWidth <= 768) {
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.marginRight = "8%";
      this.previewIframeWrap.style.marginTop = "1.5%";
    } else if (previewWidth <= 1024) {
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.marginRight = "0%";
      this.previewIframeWrap.style.marginTop = "1.5%";
    } else if (previewWidth <= 1280) {
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.marginRight = "4%";
      this.previewIframeWrap.style.marginTop = "1.5%";
    } else {
      this.previewIframeWrap.style.width = "60%";
      this.previewIframeWrap.style.marginRight = "0";
      this.previewIframeWrap.style.marginLeft = "auto";
      this.previewIframeWrap.style.removeProperty("margin-top");
    }
  }

  initObservers() {
    this.observeElement("lc-html-editor-window", ["class", "style"]);
    this.observeElement("previewiframe-wrap", ["class"], true);
    this.observeElement("previewiframe", ["style"]);
    this.observeElement("sidepanel", ["style"]);
  }

  observeElement(elementId, attributeFilter, subtree = false) {
    const element = document.getElementById(elementId);
    if (element) {
      this.observer.observe(element, { attributes: true, attributeFilter, subtree });
    }
  }
}

document.addEventListener("DOMContentLoaded", () => {
  new LayoutObserver();
});
