import React, {Fragment} from "react";
import { Menu, Transition } from '@headlessui/react'
import DropDownProps from "../../../lib/ui/types/drop-down/drop-down-props";
import clsx from "clsx";

export default class DropDown extends React.Component<DropDownProps, any> {

    constructor(props: DropDownProps) {
        super(props);
    }

    renderMenuItems() {
        return this.props.menu_items.map((menuItem) => {
            return (
                <Menu.Item disabled={this.props.disabled}>
                    {({ active }) => (
                        <button
                            className={clsx('group flex rounded-sm items-center w-full py-2 text-sm', {
                                'bg-blue-500 dark:bg-blue-600 text-white' : active
                            }, {
                                'text-gray-900 dark:text-white' : !active && this.props.selected_name !== menuItem.name
                            }, {
                                'bg-blue-500 dark:bg-blue-600 text-white': this.props.selected_name === menuItem.name
                            }, {
                                'bg-green-600 dark:bg-green-700 text-white': this.props.secondary_selected === menuItem.name
                            }, {
                                'px-4': !menuItem.hasOwnProperty('icon_class')
                            }, {
                                'px-2': menuItem.hasOwnProperty('icon_class')
                            })}
                            onClick={() => menuItem.on_click(menuItem.name)}
                            disabled={this.props.disabled}
                        >
                            {
                                typeof menuItem.icon_class !== 'undefined' ?
                                    <i className={menuItem.icon_class + " w-5 h-5 mr-2"} aria-hidden="true"></i>
                                : null
                            }

                            {menuItem.name}
                        </button>
                    )}
                </Menu.Item>
            )
        })
    }

    render() {
        return (
            <div className="my-4">
                <Menu as="div" className="relative inline-block text-left">
                    <div>
                        <Menu.Button className="inline-flex justify-center w-full px-4 py-2 text-sm font-medium rounded-sm
                        focus:outline-none focus-visible:ring-2 focus-visible:ring-blue-200 dark:focus-visible:ring-white focus-visible:ring-opacity-75 hover:bg-blue-700
                        hover:drop-shadow-md dark:text-white hover:text-gray-300 bg-blue-600 dark:bg-blue-700 text-white dark:hover:bg-blue-600
                        dark:hover:text-white font-semibold py-2 px-4 rounded-sm drop-shadow-sm disabled:bg-blue-400 dark:disabled:bg-blue-400"
                                     disabled={this.props.disabled}
                        >
                            {this.props.button_title}
                            <i className="fas fa-chevron-down w-5 h-5 ml-2 -mr-1 text-white mt-1" aria-hidden="true"></i>
                        </Menu.Button>
                    </div>
                    <Transition
                        as={Fragment}
                        enter="transition ease-out duration-100"
                        enterFrom="transform opacity-0 scale-95"
                        enterTo="transform opacity-100 scale-100"
                        leave="transition ease-in duration-75"
                        leaveFrom="transform opacity-100 scale-100"
                        leaveTo="transform opacity-0 scale-95"
                    >
                        <Menu.Items className={clsx(
                            "absolute right-0 left-[5px] z-50 w-56 mt-2 origin-top-right dark:bg-gray-700 " +
                            "bg-white divide-y dark:divide-gray-600 divide-gray-300 rounded-md shadow-lg ring-1 " +
                            "ring-black ring-opacity-5 focus:outline-none",
                            {'absolute': !this.props.use_relative, 'relative': this.props.use_relative}
                        )}>
                            {this.renderMenuItems()}
                        </Menu.Items>
                    </Transition>
                </Menu>
            </div>
        )
    }
}
