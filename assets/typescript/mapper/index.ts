import hljs from 'highlight.js';

import 'highlight.js/styles/a11y-light.css';

document.querySelectorAll('pre > code').forEach((element) => {
  hljs.highlightElement(element as HTMLElement);
});