import { authService } from "@/services/authService";
import { ROLES } from "@/enums/roles";

export function createRoleGuard(allowedRoles = []) {
  return (to, from, next) => {
    const userRole = authService.getUserRole();

    if (!userRole) {
      next("/login");
      return;
    }

    if (allowedRoles.length === 0 || allowedRoles.includes(userRole)) {
      next();
    } else {
      next("/dashboard");
    }
  };
}

export function isRoleAllowed(userRole, allowedRoles) {
  return allowedRoles.includes(userRole);
}
