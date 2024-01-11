import _Vue from 'vue';
/* eslint-disable @typescript-eslint/no-unused-vars */
export default {
  install (Vue: typeof _Vue, options?: object) {
    Vue.config.globalProperties.can = function (permission: string) {
      const permissions = this.$page.props.permissions.permissions
      let _return = false;
      if (!Array.isArray(permissions)) {
        return false;
      }
      if (permission.includes('|')) {
          permission.split('|').forEach(function (item) {
          if (permissions.includes(item.trim())) {
            _return = true;
          }
        });
      } else if (permission.includes('&')) {
        _return = true;
          permission.split('&').forEach(function (item) {
          if (!permissions.includes(item.trim())) {
            _return = false;
          }
        });
      } else {
        _return = permissions.includes(permission.trim());
      }
      return _return;
    }

    Vue.config.globalProperties.is = function (role: string) {
      const roles = this.$page.props.permissions.roles;
      let _return = false;

      if (!Array.isArray(roles)) {
        return false;
      }
      if (role.includes('|')) {
          role.split('|').forEach(function (item) {
          if (roles.includes(item.trim())) {
            _return = true;
          }
        });
      } else if (role.includes('&')) {
        _return = true;
          role.split('&').forEach(function (item) {
          if (!roles.includes(item.trim())) {
            _return = false;
          }
        });
      } else {
        _return = roles.includes(role.trim());
      }
      return _return;
    }
  }
}
