export const apiResponseFormat = {
  login: {
    message: "Login bem-sucedido",
    token: "2|I3crxhWi3XSeUs2ElSNdgkbTeNTzv7uz3ua2Rn0x5aa26b20",
  },

  register: {
    message: "Usuário registrado com sucesso",
    token: "2|I3crxhWi3XSeUs2ElSNdgkbTeNTzv7uz3ua2Rn0x5aa26b20",
  },

  logout: {
    message: "Logout realizado com sucesso",
  },

  error: {
    message: "Erro ao fazer login",
    errors: {
      email: ["Email inválido"],
      password: ["Senha inválida"],
    },
  },
};
