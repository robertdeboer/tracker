import axios from "axios";

export interface ChartedHoursInterface {
    labels: Array<string>;
    data: Array<number>;
}

const getHoursCharted = async function (
    projectId: number,
    startDate: string,
    endDate: string,
): Promise<ChartedHoursInterface> {
    const {
        data: {
            data: { projectHoursCharted },
            errors = [],
        },
    } = await axios({
        url: "/graphql",
        method: "post",
        data: {
            query: `
              query($project_id: Int!, $start: DateTime!, $end: DateTime!) {
                    projectHoursCharted(
                        input: { project_id: $project_id, start: $start, end: $end }
                    ) {
                        labels
                        data
                    }
                }`,
            variables: {
                project_id: projectId,
                start: startDate,
                end: endDate,
            },
        },
    });
    if (errors.length > 0) {
        throw new Error(errors[0].message);
    }
    return projectHoursCharted;
};

export { getHoursCharted };
