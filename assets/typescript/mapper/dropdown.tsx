import { Menu, Transition } from '@headlessui/react';
import { ChevronDownIcon } from '@heroicons/react/solid';
import React, { Fragment } from 'react';
import { render } from 'react-dom';

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

function classNames(...classes: string[]) {
  return classes.filter(Boolean).join(' ');
}

function Dropdown(prop: { id: number; label: string }) {
  return (
    <span className='relative z-0 inline-flex shadow-sm rounded-md'>
      <a
        target='_blank'
        href={`https://www.openstreetmap.org/changeset/${prop.id}`}
        className='relative inline-flex items-center px-4 py-2 rounded-l-md border border-gray-300 bg-white text-sm font-medium text-gray-700 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500'
      >
        {prop.label}
      </a>
      <Menu as='span' className='-ml-px relative block'>
        <Menu.Button className='relative inline-flex items-center px-2 py-2 rounded-r-md border border-gray-300 bg-white text-sm font-medium text-gray-500 hover:bg-gray-50 focus:z-10 focus:outline-none focus:ring-1 focus:ring-indigo-500 focus:border-indigo-500'>
          <span className='sr-only'>Open options</span>
          <ChevronDownIcon className='h-5 w-5' aria-hidden='true' />
        </Menu.Button>
        <Transition
          as={Fragment}
          enter='transition ease-out duration-100'
          enterFrom='transform opacity-0 scale-95'
          enterTo='transform opacity-100 scale-100'
          leave='transition ease-in duration-75'
          leaveFrom='transform opacity-100 scale-100'
          leaveTo='transform opacity-0 scale-95'
        >
          <Menu.Items className='origin-top-right absolute right-0 mt-2 -mr-1 w-56 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 focus:outline-none'>
            <div className='py-1'>
              {getItems(prop.id).map((item) => (
                <Menu.Item key={item.name}>
                  {({ active }) => (
                    <a
                      target='_blank'
                      href={item.href}
                      className={classNames(
                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                        'block px-4 py-2 text-sm'
                      )}
                    >
                      {item.name}
                    </a>
                  )}
                </Menu.Item>
              ))}
            </div>
          </Menu.Items>
        </Transition>
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
