import { reactive } from "vue";
import { authService } from "@/services/authService";

const state = reactive({
  isAuthenticated: authService.isAuthenticated(),
  token: authService.getToken(),
  user: authService.getUser(),
  role: authService.getUserRole(),
});

export function useAuth() {
  const login = async (email, password) => {
    const response = await authService.login(email, password);
    const { token, user } = response.data;
    authService.setToken(token);
    authService.setUser(user);
    state.token = token;
    state.user = user;
    state.role = user.role;
    state.isAuthenticated = true;
    return response.data;
  };

  const register = async (name, email, password, password_confirmation) => {
    const response = await authService.register(
      name,
      email,
      password,
      password_confirmation
    );
    const { token, user } = response.data;
    authService.setToken(token);
    authService.setUser(user);
    state.token = token;
    state.user = user;
    state.role = user.role;
    state.isAuthenticated = true;
    return response.data;
  };

  const logout = async () => {
    await authService.logout();
    state.token = null;
    state.user = null;
    state.role = null;
    state.isAuthenticated = false;
  };

  return {
    state,
    login,
    register,
    logout,
  };
}
