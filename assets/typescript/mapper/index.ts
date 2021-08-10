import { CodeJar } from 'codejar';
import hljs from 'highlight.js';

import 'highlight.js/styles/a11y-light.css';

const highlight = (editor: HTMLElement): void => {
  // highlight.js does not trims old tags,
  // let's do it by this hack.
  editor.textContent = editor.textContent
  hljs.highlightElement(editor)
}

const element = document.querySelector('.editor');
if (element !== null) {
  const jar = CodeJar(element as HTMLElement, highlight);

  document.getElementById('template-copy')?.addEventListener('click', async () => {
    await navigator.clipboard.writeText(jar.toString());

    alert('Copy to clipboard!');
  });

  document.getElementById('template-send')?.addEventListener('click', () => {
    const title = 'Test';
    const body = jar.toString();

    const url = new URL('https://www.openstreetmap.org/message/new/diog');
    url.searchParams.set('message[title]', title);
    url.searchParams.set('message[body]', body);

    window.open(url.toString());
  });
};
