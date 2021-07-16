import { ActionContext } from "vuex";
import { AxiosResponse } from "axios";

import { IRootState } from "../../interfaces/state";
import { IPagination, Pagination } from "../../interfaces/pagination";

import { IListResponse } from "../../api";

import { ILdapEntryState } from "./state";
import { ILdapEntry } from "./interfaces";

/**
 * LdapEntry API actions interface.
 */
export interface ILdapEntryActions {
  getAll({ commit, state }: ActionContext<ILdapEntryState, IRootState>, { page, size }: IPagination): Promise<any>;
  get({ commit, state }: ActionContext<ILdapEntryState, IRootState>, id: string): Promise<any>;
  create({ commit, state }: ActionContext<ILdapEntryState, IRootState>, entry: ILdapEntry): Promise<any>;
  update({ commit, state }: ActionContext<ILdapEntryState, IRootState>, entry: ILdapEntry): Promise<any>;
  delete({ commit, state }: ActionContext<ILdapEntryState, IRootState>, id: string): Promise<any>;
}

export const LdapEntryActionsDefault: ILdapEntryActions = {
  async getAll(
    { commit, state }: ActionContext<ILdapEntryState, IRootState>,
    { page, size, criteria = null, orderBy = null }: IPagination = new Pagination()
  ) {
    commit("GET_ALL_PENDING");
    try {
      const response: AxiosResponse<IListResponse<ILdapEntry>> = await state.api.getAll(page, size, criteria, orderBy);
      commit("GET_ALL_SUCCESS", response.data);
      return response.data;
    } catch (error) {
      commit("GET_ALL_ERROR", error);
      return error;
    }
  },

  async get(
    { commit, state }: ActionContext<ILdapEntryState, IRootState>,
    dn: string
  ) {
    commit("GET_PENDING");
    try {
      const response: AxiosResponse<ILdapEntry> = await state.api.get(dn);
      commit("GET_SUCCESS", response.data);
      return response.data;
    } catch (error) {
      commit("GET_ERROR", error);
      return error;
    }
  },

  async create(
    { commit, state }: ActionContext<ILdapEntryState, IRootState>,
    entry: ILdapEntry
  ) {
    commit("CREATE_PENDING");
    try {
      const response: AxiosResponse<ILdapEntry> = await state.api.create(entry);
      commit("CREATE_SUCCESS", response.data);
      return response.data;
    } catch (error) {
      commit("CREATE_ERROR", error);
      return error;
    }
  },

  async update(
    { commit, state }: ActionContext<ILdapEntryState, IRootState>,
    entry: ILdapEntry
  ) {
    commit("EDIT_PENDING");
    try {
      const response: AxiosResponse<ILdapEntry> = await state.api.update(entry.dn, entry);
      commit("EDIT_SUCCESS", response.data);
      return response.data;
    } catch (error) {
      commit("EDIT_ERROR", error);
      return error;
    }
  },

  async delete(
    { commit, state }: ActionContext<ILdapEntryState, IRootState>,
    dn: string
  ) {
    commit("DELETE_PENDING");
    try {
      const response: AxiosResponse<void> = await state.api.delete(dn);
      commit("DELETE_SUCCESS", dn);
      return response.data;
    } catch (error) {
      commit("DELETE_ERROR", error);
      return error;
    }
  },

};
