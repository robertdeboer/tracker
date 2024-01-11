export type ErrorType = {
    errors: Array<{ message: string }>;
};

export interface ErrorInterface {
    errors: Array<{ message: string }>;
}

export const enum OrderDirection {
    ASC = "ASC",
    DESC = "DESC",
}

export interface OrderByInterface {
    column: string;
    order: string;
}

export type PaginationType = {
    count: number;
    currentPage: number;
    firstItem: number;
    hasMorePages: boolean;
    lastItem: number;
    lastPage: number;
    perPage: number;
    total: number;
};

export interface PaginationInterface {
    count: number;
    currentPage: number;
    firstItem: number;
    hasMorePages: boolean;
    lastItem: number;
    lastPage: number;
    perPage: number;
    total: number;
}

export interface WhereInterface {
    column: string;
    operator: string;
    /* eslint-disable-next-line @typescript-eslint/no-explicit-any */
    value: any;
}

export interface ComplexWhereInterface {
    AND?: Array<WhereInterface>;
    OR?: Array<WhereInterface>;
}
