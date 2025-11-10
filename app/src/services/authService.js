import apiClient from "./apiClient";

export const authService = {
  login(email, password) {
    return apiClient.post("/login", { email, password });
  },

  register(name, email, password, password_confirmation) {
    return apiClient.post("/register", {
      name,
      email,
      password,
      password_confirmation,
    });
  },

  logout() {
    return apiClient.post("/logout").finally(() => {
      localStorage.removeItem("authToken");
      localStorage.removeItem("authUser");
    });
  },

  getToken() {
    return localStorage.getItem("authToken");
  },

  setToken(token) {
    localStorage.setItem("authToken", token);
  },

  getUser() {
    const user = localStorage.getItem("authUser");
    return user ? JSON.parse(user) : null;
  },

  setUser(user) {
    localStorage.setItem("authUser", JSON.stringify(user));
  },

  getUserRole() {
    const user = this.getUser();
    return user ? user.role : null;
  },

  clearToken() {
    localStorage.removeItem("authToken");
    localStorage.removeItem("authUser");
  },

  isAuthenticated() {
    return !!this.getToken();
  },
};
