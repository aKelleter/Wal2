// main.js (module)
import { initNavigation } from './navigation.js';
import { initBtnTop } from './btn-top.js';
import { initHighlight } from './highlight.js';
import { initCopyButtons } from './copy-button.js';
import { initCleanAlert } from './clean-alert.js';

document.addEventListener("DOMContentLoaded", function () {
  console.log("main.js (module) chargé");
  initNavigation();
  initBtnTop();
  initHighlight();
  initCopyButtons();
  initCleanAlert();
});
