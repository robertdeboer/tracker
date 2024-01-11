export interface TablePaginationInterface {
    numberOfRows: number;
    page: number;
}

export interface TableSortInterface {
    column: string;
    order: string;
}

export interface DropdownInterface {
    code: number | string;
    name: string;
}
