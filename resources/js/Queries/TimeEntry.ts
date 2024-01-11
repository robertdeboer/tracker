import axios from "axios";
import { TimeEntryInterface, TimeEntryWriteInterface } from "@/Types/TimeEntry";
import { PaginationInterface } from "@/Types/Query";
import { TableSortInterface } from "@/Types/Page";

export interface TimeEntriesResponseInterface {
    data: Array<TimeEntryInterface>;
    paginatorInfo: PaginationInterface;
}

/**
 * Create a time entry
 * @param entry<TimeEntryWriteInterface>
 * @param workItemId<number>
 */
const createTimeEntry = async function (
    entry: TimeEntryWriteInterface,
    workItemId: number,
): Promise<TimeEntryInterface> {
    const {
        data: { timeEntryCreate, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($input: TimeEntryCreateInput!) {
                timeEntryCreate(
                    input: $input
                ) {
                    id
                }
            }`,
            variables: {
                input: {
                    ...entry,
                    work_item: { connect: workItemId },
                    author: { connect: null },
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return timeEntryCreate;
};
/**
 * Delete a time entry
 * @param id
 */
const deleteTimeEntry = async function (
    id: number,
): Promise<TimeEntryInterface> {
    const {
        data: { timeEntryDelete, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($id: ID!) {
                timeEntryDelete(id: $id) {
                    id
                }
            }`,
            variables: {
                id: id,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return timeEntryDelete;
};
/**
 * Get a time entry
 * @param id<number>
 */
const getTimeEntry = async function (id: number): Promise<TimeEntryInterface> {
    const {
        data: { data: { timeEntry = {} } = {}, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `query($input: TimeEntryInput!) {
                    timeEntry(input: $input) {
                        id
                        hours
                        date
                        note
                        author {
                            first_name
                            last_name
                            email
                        }
                        created_at
                        updated_at
                    }
                }`,
            variables: {
                input: { id: id },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return timeEntry;
};
/**
 * Get time entries for a work item
 * @param workItemId<number>
 * @param first<number>
 * @param page<number>
 * @param sort<Array<TableSortInterface>>
 */
const getTimeEntries = async function (
    workItemId: number,
    first: number = 10,
    page: number = 1,
    sort: Array<TableSortInterface> = [{ column: "ID", dir: "ASC" }],
): Promise<TimeEntriesResponseInterface> {
    const {
        data: { data: { timeEntries = [] } = {}, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `query($workItem: QueryTimeEntriesHasWorkItemWhereHasConditions, $first: Int!, $page: Int!, $orderBy: [QueryTimeEntriesOrderByOrderByClause!]) {
                timeEntries(hasWorkItem: $workItem, first: $first, page: $page, orderBy: $orderBy) {
                    paginatorInfo {
                        count
                        total
                    }
                    data {
                        id
                        author {
                            first_name
                            last_name
                        }
                        hours
                        date
                        note
                    }
                }
            }`,
            variables: {
                workItem: { column: "ID", operator: "EQ", value: workItemId },
                first: first,
                page: page,
                orderBy: sort,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return timeEntries;
};
/**
 * Rebate a time entry
 * @param id<number>
 */
const rebateTimeEntry = async function (
    id: number,
): Promise<TimeEntryInterface> {
    const {
        data: { timeEntryRebate, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($id: ID!) {
                timeEntryRebate(id: $id) {
                    id
                }
            }`,
            variables: {
                id: id,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return timeEntryRebate;
};

/**
 * Update a time entry
 * @param data<TimeEntryWriteInterface>
 */
const updateTimeEntry = async function (
    data: TimeEntryWriteInterface,
): Promise<TimeEntryInterface> {
    const {
        data: { updateTimeEntry, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `mutation($input: TimeEntryUpdateInput!) { timeEntryUpdate(input: $input) { id } }`,
            variables: {
                input: data,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return updateTimeEntry;
};

export {
    createTimeEntry,
    deleteTimeEntry,
    getTimeEntry,
    getTimeEntries,
    rebateTimeEntry,
    updateTimeEntry,
};
