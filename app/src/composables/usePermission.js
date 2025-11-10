import { useAuth } from "@/composables/useAuth";
import {
  ROLES,
  hasRole,
  hasAnyRole,
  isAdmin,
  isAttendant,
  isClient,
} from "@/enums/roles";

export function usePermission() {
  const { state } = useAuth();

  const canAccess = (requiredRole) => {
    return hasRole(state.role, requiredRole);
  };

  const canAccessAny = (roles = []) => {
    return hasAnyRole(state.role, roles);
  };

  const canEditUser = () => {
    return state.role === ROLES.ADMINISTRADOR || state.role === ROLES.ATENDENTE;
  };

  const canDeleteUser = () => {
    return state.role === ROLES.ADMINISTRADOR;
  };

  const canViewReports = () => {
    return state.role === ROLES.ADMINISTRADOR || state.role === ROLES.ATENDENTE;
  };

  const canViewExclusive = () => {
    return state.role === ROLES.ADMINISTRADOR;
  };

  const canManageRentals = () => {
    return state.role === ROLES.ADMINISTRADOR || state.role === ROLES.ATENDENTE;
  };

  const isAdmin = () => {
    return state.role === ROLES.ADMINISTRADOR;
  };

  const isAttendant = () => {
    return state.role === ROLES.ATENDENTE;
  };

  const isClient = () => {
    return state.role === ROLES.CLIENTE;
  };

  return {
    canAccess,
    canAccessAny,
    canEditUser,
    canDeleteUser,
    canViewReports,
    canViewExclusive,
    canManageRentals,
    isAdmin,
    isAttendant,
    isClient,
    role: state.role,
  };
}
