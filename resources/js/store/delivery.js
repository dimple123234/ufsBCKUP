import { createStore } from 'vuex';

const store = createStore({
  state: {
    deliveryStatus: false,
    deliveryMessage: '',
    timeRemaining: 600,
    isActive: false
  },
  mutations: {
    SET_DELIVERY_STATUS(state, { status, message, isActive }) {
      state.deliveryStatus = status;
      state.deliveryMessage = message;
      state.isActive = isActive;
    },
    SET_TIME_REMAINING(state, time) {
      state.timeRemaining = time;
    }
  },
  actions: {
    updateDeliveryStatus({ commit }, { status, message, isActive }) {
      commit('SET_DELIVERY_STATUS', { status, message, isActive });
    },
    updateTimeRemaining({ commit }, time) {
      commit('SET_TIME_REMAINING', time);
    }
  }
});

export default store;
