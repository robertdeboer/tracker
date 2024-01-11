const Ziggy = {
    url: "http://localhost",
    port: null,
    defaults: {},
    routes: {
        login: { uri: "login", methods: ["GET", "HEAD"] },
        logout: { uri: "logout", methods: ["POST"] },
        "password.request": {
            uri: "forgot-password",
            methods: ["GET", "HEAD"],
        },
        "password.reset": {
            uri: "reset-password/{token}",
            methods: ["GET", "HEAD"],
            parameters: ["token"],
        },
        "password.email": { uri: "forgot-password", methods: ["POST"] },
        "password.update": { uri: "reset-password", methods: ["POST"] },
        "verification.notice": {
            uri: "email/verify",
            methods: ["GET", "HEAD"],
        },
        "verification.verify": {
            uri: "email/verify/{id}/{hash}",
            methods: ["GET", "HEAD"],
            parameters: ["id", "hash"],
        },
        "verification.send": {
            uri: "email/verification-notification",
            methods: ["POST"],
        },
        "user-profile-information.update": {
            uri: "user/profile-information",
            methods: ["PUT"],
        },
        "user-password.update": { uri: "user/password", methods: ["PUT"] },
        "password.confirmation": {
            uri: "user/confirmed-password-status",
            methods: ["GET", "HEAD"],
        },
        "password.confirm": { uri: "user/confirm-password", methods: ["POST"] },
        "two-factor.login": {
            uri: "two-factor-challenge",
            methods: ["GET", "HEAD"],
        },
        "two-factor.enable": {
            uri: "user/two-factor-authentication",
            methods: ["POST"],
        },
        "two-factor.confirm": {
            uri: "user/confirmed-two-factor-authentication",
            methods: ["POST"],
        },
        "two-factor.disable": {
            uri: "user/two-factor-authentication",
            methods: ["DELETE"],
        },
        "two-factor.qr-code": {
            uri: "user/two-factor-qr-code",
            methods: ["GET", "HEAD"],
        },
        "two-factor.secret-key": {
            uri: "user/two-factor-secret-key",
            methods: ["GET", "HEAD"],
        },
        "two-factor.recovery-codes": {
            uri: "user/two-factor-recovery-codes",
            methods: ["GET", "HEAD"],
        },
        "terms.show": { uri: "terms-of-service", methods: ["GET", "HEAD"] },
        "policy.show": { uri: "privacy-policy", methods: ["GET", "HEAD"] },
        "profile.show": { uri: "user/profile", methods: ["GET", "HEAD"] },
        "other-browser-sessions.destroy": {
            uri: "user/other-browser-sessions",
            methods: ["DELETE"],
        },
        "current-user-photo.destroy": {
            uri: "user/profile-photo",
            methods: ["DELETE"],
        },
        "current-user.destroy": { uri: "user", methods: ["DELETE"] },
        "sanctum.csrf-cookie": {
            uri: "sanctum/csrf-cookie",
            methods: ["GET", "HEAD"],
        },
        graphql: { uri: "graphql", methods: ["GET", "POST", "HEAD"] },
        "ignition.healthCheck": {
            uri: "_ignition/health-check",
            methods: ["GET", "HEAD"],
        },
        "ignition.executeSolution": {
            uri: "_ignition/execute-solution",
            methods: ["POST"],
        },
        "ignition.updateConfig": {
            uri: "_ignition/update-config",
            methods: ["POST"],
        },
        welcome: { uri: "/", methods: ["GET", "HEAD"] },
        dashboard: { uri: "dashboard", methods: ["GET", "HEAD"] },
        orders: { uri: "orders", methods: ["GET", "HEAD"] },
        project: {
            uri: "project/{id}",
            methods: ["GET", "HEAD"],
            wheres: { id: "[0-9]+" },
            parameters: ["id"],
        },
        "project.log": {
            uri: "project/{id}/log",
            methods: ["GET", "HEAD"],
            wheres: { id: "[0-9]+" },
            parameters: ["id"],
        },
        "project.summary": {
            uri: "project/{id}/summary",
            methods: ["GET", "HEAD"],
            parameters: ["id"],
        },
        users: { uri: "users", methods: ["GET", "HEAD"] },
    },
};

if (typeof window !== "undefined" && typeof window.Ziggy !== "undefined") {
    Object.assign(Ziggy.routes, window.Ziggy.routes);
}

export { Ziggy };
