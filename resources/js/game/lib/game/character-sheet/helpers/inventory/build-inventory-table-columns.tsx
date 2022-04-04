import React, {Component, MouseEventHandler} from "react";
import ItemNameColorationButton from "../../../../../components/ui/item-name-coloration-button";
import { formatNumber } from "../../../format-number";
import ActionsInterface from "./actions-interface";
import InventoryDetails from "../../types/inventory/inventory-details";

export const BuildInventoryTableColumns = (component?: ActionsInterface, clickAction?: (item: InventoryDetails) => any) => {
    const columns = [
        {
            name: 'Name',
            selector: (row: { item_name: string; }) => row.item_name,
            sortable: true,
            cell: (row: any) => <ItemNameColorationButton item={row} on_click={clickAction} />
        },
        {
            name: 'Type',
            selector: (row: { type: string; }) => row.type,
            sortable: true,
        },
        {
            name: 'Attack',
            selector: (row: { attack: number; }) => row.attack,
            sortable: true,
            format: (row: any) => formatNumber(row.attack)
        },
        {
            name: 'AC',
            selector: (row: { ac: number; }) => row.ac,
            sortable: true,
            format: (row: any) => formatNumber(row.ac)
        },
    ];

    if (typeof component !== 'undefined') {
        columns.push({
            name: 'Actions',
            selector: (row: any) => '',
            sortable: true,
            cell: (row: any) => component.actions(row)
        });
    }

    return columns;
}

export const buildLimitedColumns = (component?: ActionsInterface) => {
        const columns = [
            {
                name: 'Name',
                selector: (row: { item_name: string; }) => row.item_name,
                sortable: true,
                cell: (row: any) => <ItemNameColorationButton item={row} />
            },
            {
                name: 'Description',
                selector: (row: { description: string; }) => row.description,
                sortable: true,
                cell: (row: any) => row.description
            },
        ];

        if (typeof component !== 'undefined') {
            columns.push({
                name: 'Actions',
                selector: (row: any) => '',
                sortable: true,
                cell: (row: any) => component.actions(row)
            });
        }

        return columns
}
