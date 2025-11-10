export const createGenericService = (endpoint) => {
  const apiClient = require("./apiClient").default;

  return {
    getAll(params = {}) {
      return apiClient.get(`/${endpoint}`, { params });
    },

    getById(id) {
      return apiClient.get(`/${endpoint}/${id}`);
    },

    create(data) {
      return apiClient.post(`/${endpoint}`, data);
    },

    update(id, data) {
      return apiClient.put(`/${endpoint}/${id}`, data);
    },

    delete(id) {
      return apiClient.delete(`/${endpoint}/${id}`);
    },
  };
};
