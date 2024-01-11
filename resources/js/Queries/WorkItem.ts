import axios from "axios";
import { PaginationInterface } from "@/Types/Query";
import { WorkItemInterface, WorkItemWriteInterface } from "@/Types/WorkItem";
import { isBoolean, isEmpty } from "lodash";
import { UserInterface } from "@/Types/User";

export interface WorkItemsResponseInterface {
    data: Array<WorkItemInterface>;
    paginatorInfo: PaginationInterface;
}

export interface WorkItemsSearchInputInterface {
    name?: string;
    is_open?: boolean;
    start_date?: {
        from: string;
        to: string;
    };
    end_date?: {
        from: string;
        to: string;
    };
    project_id?: number;
}

export interface WorkItemOrderByInterface {
    column: string;
    order: string;
}

export interface WorkItemFilterInterface {
    name?: {
        value: string | null;
    };
    is_open?: {
        value: boolean | null;
    };
}

/**
 * Create a work item
 */
const createWorkItem = async function (
    form: WorkItemWriteInterface,
): Promise<WorkItemInterface> {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { id = 0, owner_id, project_id, ...data } = form;
    const {
        data: {
            data: { workItemCreate = {} },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($input: WorkItemCreateInput!) {
                workItemCreate(
                    input: $input
                ) {
                    id
                }
            }`,
            variables: {
                input: {
                    ...data,
                    project: { connect: project_id },
                    owner: { connect: owner_id },
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return workItemCreate;
};
/**
 * Delete a work item
 * @param id<number>
 */
const deleteWorkItem = async function (id: number): Promise<WorkItemInterface> {
    const {
        data: { workItemDelete = {}, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($id: ID!) {
                workItemDelete(id: $id) {
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
    return workItemDelete;
};
/**
 * Get all users who quality to be a work time owner
 */
const getAvailableWorkItemOwners = async function (): Promise<
    Array<UserInterface>
> {
    const {
        data: {
            data: { availableWorkItemOwners = [] },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query {
            availableWorkItemOwners {
                id
                first_name
                last_name
            }
        }`,
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return availableWorkItemOwners;
};
/**
 * Get a work item
 * @param id<number>
 */
const getWorkItem = async function (id: number): Promise<WorkItemInterface> {
    const {
        data: {
            data: { workItem = {} },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `query($input: WorkItemInput!) {
                workItem(input: $input) {
                    id
                    name
                    is_open
                    start_date
                    end_date
                    owner {
                        id
                    }
                    ticket_data {
                        id
                        url
                    }
                }
            }`,
            variables: {
                input: {
                    id: id,
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return workItem;
};
/**
 * Get a list of work items
 * @param first<number>
 * @param page<number>
 * @param input<WorkItemsSearchInputInterface>
 * @param orderBy<Array<WorkItemOrderByInterface>>
 * @param filters<WorkItemFilterInterface>
 */
const getWorkItems = async function (
    first: number = 10,
    page: number = 1,
    input: WorkItemsSearchInputInterface = {},
    orderBy: Array<WorkItemOrderByInterface> = [
        { column: "START_DATE", order: "ASC" },
    ],
    filters: WorkItemFilterInterface = {},
): Promise<WorkItemsResponseInterface> {
    if (!isEmpty(filters.name?.value)) {
        input = { ...input, name: `%${filters.name?.value}%` };
    }
    if (isBoolean(filters.is_open?.value)) {
        input = { ...input, is_open: filters.is_open?.value };
    }
    const {
        data: { errors = [], data: { workItems = {} } = {} },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
              query($first: Int!, $page: Int!, $input: WorkItemSearchInput, $orderBy: [QueryWorkItemsOrderByOrderByClause!]) {
                workItems(first: $first, page: $page, input: $input, orderBy: $orderBy) {
                    paginatorInfo {
                        total
                        perPage
                    }
                    data {
                        id
                        name
                        is_open
                        start_date
                        end_date
                        created_at
                        updated_at
                        owner {
                            id
                            first_name
                            last_name
                        }
                        ticket_data {
                            id
                            url
                        }
                        hours
                    }
                }
            }`,
            variables: {
                first: first,
                page: page,
                input: input,
                orderBy: orderBy,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return workItems;
};
/**
 * Update a work item
 */
const updateWorkItem = async function (
    workItem: WorkItemWriteInterface,
): Promise<WorkItemInterface> {
    const { owner_id, project_id, ...update } = workItem;
    const {
        data: {
            data: { workItemUpdate },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `mutation($input: WorkItemUpdateInput!) {
                workItemUpdate(
                    input: $input
                ) {
                    id
                    name
                    is_open
                    start_date
                    end_date
                    hours
                    created_at
                    updated_at
                }
            }`,
            variables: {
                input: {
                    ...update,
                    owner: { connect: owner_id },
                    project: { connect: project_id },
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return workItemUpdate;
};
export {
    createWorkItem,
    deleteWorkItem,
    getAvailableWorkItemOwners,
    getWorkItem,
    getWorkItems,
    updateWorkItem,
};
