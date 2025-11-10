export const ROLES = {
  CLIENTE: "cliente",
  ATENDENTE: "atendente",
  ADMINISTRADOR: "administrador",
};

export const ROLE_LABELS = {
  [ROLES.CLIENTE]: "Cliente",
  [ROLES.ATENDENTE]: "Atendente",
  [ROLES.ADMINISTRADOR]: "Administrador",
};

export const hasRole = (userRole, requiredRole) => {
  return userRole === requiredRole;
};

export const hasAnyRole = (userRole, roles = []) => {
  return roles.includes(userRole);
};

export const isAdmin = (userRole) => {
  return userRole === ROLES.ADMINISTRADOR;
};

export const isAttendant = (userRole) => {
  return userRole === ROLES.ATENDENTE;
};

export const isClient = (userRole) => {
  return userRole === ROLES.CLIENTE;
};
