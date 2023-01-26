import { Menu } from '@headlessui/react';
import { ChevronDownIcon } from '@heroicons/react/20/solid';
import { createPopper, Options } from '@popperjs/core';
import React, {  RefCallback, useRef, useCallback, useMemo } from 'react';
import ReactDOM, { render } from 'react-dom';

function getItems(id: number) {
  return [
    {
      name: 'OSMCha',
      href: `https://osmcha.org/changesets/${id}`,
    },
    {
      name: 'achavi',
      href: `https://overpass-api.de/achavi/?changeset=${id}&relations=true`,
    },
    {
      name: 'OSM History Viewer',
      href: `https://osmhv.openstreetmap.de/changeset.jsp?id=${id}`,
    },
    {
      name: 'OSM Lab',
      href: `https://osmlab.github.io/changeset-map/#${id}`
    },
  ];
}

/** @see https://github.com/tailwindlabs/headlessui/blob/main/packages/@headlessui-react/playground-utils/hooks/use-popper.ts */
function usePopper(options?: Partial<Options>): [RefCallback<Element | null>, RefCallback<HTMLElement | null>] {
  const reference = useRef<Element>(null);
  const popper = useRef<HTMLElement>(null);

  // eslint-disable-next-line @typescript-eslint/no-empty-function
  const cleanupCallback = useRef(() => {});

  const instantiatePopper = useCallback(() => {
    if (!reference.current) return;
    if (!popper.current) return;

    if (cleanupCallback.current) cleanupCallback.current();

    cleanupCallback.current = createPopper(reference.current, popper.current, options).destroy;
  }, [reference, popper, cleanupCallback, options]);

  return useMemo(
    () => [
      referenceDomNode => {
        (reference as React.MutableRefObject<Element|null>).current = referenceDomNode;
        instantiatePopper();
      },
      popperDomNode => {
        (popper  as React.MutableRefObject<Element|null>).current = popperDomNode;
        instantiatePopper();
      },
    ],
    [reference, popper, instantiatePopper]
  );
}

function Portal(props: { children: React.ReactNode }) {
  const { children } = props;
  const [mounted, setMounted] = React.useState(false);

  React.useEffect(() => setMounted(true), []);

  if (!mounted) return null;

  return ReactDOM.createPortal(children, document.body);
}

function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(' ');
}

function Dropdown(prop: { id: number; label: string }) {
  const [trigger, container] = usePopper({
    placement: 'bottom-end',
    strategy: 'fixed',
    modifiers: [{ name: 'offset', options: { offset: [0, 5] } }],
  });

  return (
    <span className="relative z-0 inline-flex shadow-sm rounded-md">
      <a
        target="_blank" rel="noreferrer"
        href={`https://www.openstreetmap.org/changeset/${prop.id}`}
        className="relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
      >
        {prop.label}
      </a>
      <Menu as="span" className="-ml-px relative block">
        <Menu.Button
          ref={trigger}
          className="relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500"
        >
          <span className="sr-only">Open options</span>
          <ChevronDownIcon className="h-5 w-5" aria-hidden="true" />
        </Menu.Button>
        <Portal>
          <Menu.Items
            ref={container}
            className="origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none"
          >
            <div className="py-1">
              {getItems(prop.id).map((item) => (
                <Menu.Item key={item.name}>
                  {({ active }) => (
                    <a
                      target="_blank"
                      href={item.href}
                      className={classNames(
                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                        'block px-4 py-2 text-sm'
                      )} rel="noreferrer"
                    >
                      {item.name}
                    </a>
                  )}
                </Menu.Item>
              ))}
            </div>
          </Menu.Items>
        </Portal>
      </Menu>
    </span>
  );
}

export default function createDropdown(
  element: Element,
  prop: { id: number; label: string }
): void {
  render(<Dropdown id={prop.id} label={prop.label} />, element);
}
