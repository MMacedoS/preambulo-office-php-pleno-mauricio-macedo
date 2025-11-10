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
    });
  },

  getToken() {
    return localStorage.getItem("authToken");
  },

  setToken(token) {
    localStorage.setItem("authToken", token);
  },

  clearToken() {
    localStorage.removeItem("authToken");
  },

  isAuthenticated() {
    return !!this.getToken();
  },
};
