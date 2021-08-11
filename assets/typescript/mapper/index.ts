import { CodeJar } from 'codejar';
import hljs from 'highlight.js';

import 'highlight.js/styles/a11y-light.css';

/** Notes */
document.querySelectorAll('pre > code').forEach((element) => {
  hljs.highlightElement(element as HTMLElement);
});

/** Form */
const highlight = (editor: HTMLElement): void => {
  // highlight.js does not trims old tags,
  // let's do it by this hack.
  editor.textContent = editor.textContent
  hljs.highlightElement(editor)
}

const element = document.querySelector('.editor');
if (element !== null) {
  const jar = CodeJar(element as HTMLElement, highlight);

  document.getElementById('template-copy')?.addEventListener('click', async (event: MouseEvent) => {
    const button = (event.target as HTMLButtonElement);

    await navigator.clipboard.writeText(jar.toString());

    button.classList.add('text-green-500');
    setTimeout(() => { button.classList.remove('text-green-500'); }, 1500);
  });

  document.getElementById('template-form')?.addEventListener('submit', (event: Event) => {
    event.preventDefault();

    const form = (event.target as HTMLFormElement);

    const title = (document.getElementById('template-title') as HTMLInputElement)?.value;
    const body = jar.toString();

    const { mapper } = form.dataset;

    const url = new URL(`https://www.openstreetmap.org/message/new/${mapper}`);
    url.searchParams.set('message[title]', title);
    url.searchParams.set('message[body]', body);

    window.open(url.toString());
  });
};
