import axios from "axios";
import { OrderInterface, OrderWriteInterface } from "@/Types/Order";
import { isEmpty, isNull } from "lodash";
import { PaginationInterface } from "@/Types/Query";
import { DateTime } from "luxon";

export interface OrdersResponseInterface {
    data: Array<OrderInterface>;
    paginatorInfo: PaginationInterface;
}

export interface OrderFilterInterface {
    email: string | null;
    project: string | null;
    reference_number: string | null;
}

/**
 * Create an order
 * @param order<ProjectInterface>
 */
const CreateOrder = async function (
    order: OrderWriteInterface,
): Promise<OrderInterface> {
    // eslint-disable-next-line @typescript-eslint/no-unused-vars
    const { id, project_id, ...orderData } = order;
    const orderDate = DateTime.fromISO(orderData.date);
    const {
        data: { errors, orderCreate },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($input: OrderCreateInput!){
                orderCreate(input: $input) {
                    id
                    reference_number
                    date
                    email
                    hours
                    created_at
                    updated_at
                }
            }`,
            variables: {
                input: {
                    ...orderData,
                    project: { connect: project_id },
                    date: orderDate.toFormat("yyyy-MM-dd"),
                },
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return orderCreate;
};

/**
 * Delete an order
 * @param orderId<number>
 */
const DeleteOrder = async function (orderId: number): Promise<number> {
    const {
        data: {
            data: { orderDelete: { id = 0 } } = { orderDelete: {} },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `mutation($order: ID!) {
                        orderDelete(id: $order) {
                            id
                            reference_number
                            date
                            email
                            hours
                            created_at
                            updated_at
                        }
                    }`,
            variables: {
                order: orderId,
            },
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return id;
};

/**
 * Get a single order
 * @param orderId<number>
 * @constructor
 */
const GetOrder = async function (orderId: number): Promise<OrderInterface> {
    const {
        data: { data: { order } = { order: {} }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query {
                order(input: { id: "${orderId}" }) {
                    id
                    reference_number
                    date
                    email
                    hours
                    created_at
                    updated_at
                    project {
                        id
                    }
                }
            }`,
        },
    });
    if (errors?.length > 0) {
        throw new Error(errors[0].message);
    }
    return order;
};

/**
 * Get orders
 * @param first<number>
 * @param page<number>
 * @param orderBy<string>
 * @param orderByDir<string>
 * @param filters<object>
 * @param projectId<number|null>
 */
const GetOrderList = async function (
    first: number = 10,
    page: number = 1,
    orderBy: string = "CREATED_AT",
    orderByDir: string = "ASC",
    filters: OrderFilterInterface = {
        email: null,
        project: null,
        reference_number: null,
    },
    projectId: number | null = null,
): Promise<OrdersResponseInterface> {
    let input = {};
    if (!isEmpty(filters.email)) {
        input = { ...input, email: `%${filters.email}%` };
    }
    if (!isNull(projectId)) {
        input = { ...input, project_id: projectId };
    }
    if (!isEmpty(filters.reference_number)) {
        input = { ...input, reference_number: `%${filters.reference_number}%` };
    }
    let hasProject = null;
    if (!isEmpty(filters.project)) {
        hasProject = {
            column: "NAME",
            operator: "LIKE",
            value: `%${filters.project}%`,
        };
    }
    const {
        data: { data: { orders } = { orders: [] }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            query($input: OrderSearchInput, $first: Int, $page: Int, $orderBy: QueryOrdersOrderByColumn!, $orderByDir: SortOrder!, $hasProject: QueryOrdersHasProjectWhereHasConditions) {
                orders(input: $input, first: $first, page: $page, orderBy: [{column: $orderBy, order: $orderByDir}], hasProject: $hasProject) {
                    paginatorInfo {
                        count
                        currentPage
                        firstItem
                        hasMorePages
                        lastItem
                        lastPage
                        perPage
                        total
                    }
                    data {
                        id
                        reference_number
                        date
                        email
                        hours
                        project {
                            id
                            name
                            is_active
                        }
                        created_at
                        updated_at
                    }
                }
            }`,
            variables: {
                input: input,
                first: first,
                page: page,
                orderBy: orderBy,
                orderByDir: orderByDir,
                hasProject: hasProject,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return orders;
};

/**
 *
 * @param order<OrderInterface>
 * @constructor
 */
const UpdateOrder = async function (
    order: OrderWriteInterface,
): Promise<OrderInterface> {
    const { project_id, ...update } = order;
    const {
        data: { data: { orderUpdate } = { orderUpdate: {} }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
            mutation($input: OrderUpdateInput!) {
                orderUpdate(input: $input) {
                    id
                    reference_number
                    date
                    email
                    hours
                    created_at
                    updated_at
                }
            }`,
            variables: {
                input: {
                    ...update,
                    project: { connect: project_id },
                },
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return orderUpdate;
};

export { CreateOrder, DeleteOrder, GetOrder, GetOrderList, UpdateOrder };
