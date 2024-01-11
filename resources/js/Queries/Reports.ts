import axios from "axios";
import route from "ziggy-js";
import { DateTime } from "luxon";

const emailSummary = async function (
    projectId: number,
    startDate: DateTime,
    endDate: DateTime,
): Promise<boolean> {
    await axios({
        url: route("project.summary.send", {
            id: projectId,
            start: startDate.toFormat("yyyy-MM-dd"),
            end: endDate.toFormat("yyyy-MM-dd"),
        }),
        method: "post",
    });
    return true;
};

export { emailSummary };
