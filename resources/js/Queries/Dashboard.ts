import axios from "axios";
import { ProjectInterface, ProjectType } from "@/Types/Project";

export interface DashboardProductsInterface {
    dashboard: Array<ProjectType>;
}

export interface DashboardResponseInterface {
    data: {
        data: DashboardProductsInterface;
    };
}

/**
 * Get projects for the dashboard
 * @param showInActive<boolean>
 */
const getProjects = async function (showInActive: boolean) {
    const {
        data: { data: { dashboard } = { dashboard: [] }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
                query {
                    dashboard(in_active: ${showInActive}, with_hours: true) {
                        id
                        name
                        is_active
                        created_at
                        updated_at
                        customer {
                            id
                            first_name
                            last_name
                            email
                        }
                        hours_ordered
                        hours_used
                    }
                }`,
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return dashboard;
};
/**
 * Get projects for the main nav
 * @param showInActive
 */
const getProjectsForNavigation = async function (
    showInActive: boolean = false,
): Promise<ProjectInterface> {
    const {
        data: { data: { dashboard } = { dashboard: [] }, errors = [] },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
                query {
                    dashboard(in_active: ${showInActive}, with_hours: false) {
                        id
                        name
                    }
                }`,
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return dashboard;
};

export { getProjects, getProjectsForNavigation };
