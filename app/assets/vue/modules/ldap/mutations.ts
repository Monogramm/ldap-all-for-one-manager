import { AxiosError } from "axios";

import { IListResponse } from "../../api";

import { ILdapEntryState } from "./state";
import { ILdapEntry } from "./interfaces";

export interface ILdapEntryMutations {
  GET_ALL_PENDING(state: ILdapEntryState): void;
  GET_ALL_SUCCESS(state: ILdapEntryState, data: IListResponse<ILdapEntry>): void;
  GET_ALL_ERROR(state: ILdapEntryState, error?: AxiosError): void;

  GET_PENDING(state: ILdapEntryState): void;
  GET_SUCCESS(state: ILdapEntryState, data: ILdapEntry): void;
  GET_ERROR(state: ILdapEntryState, error?: AxiosError): void;

  CREATE_PENDING(state: ILdapEntryState): void;
  CREATE_SUCCESS(state: ILdapEntryState, data: ILdapEntry): void;
  CREATE_ERROR(state: ILdapEntryState, error?: AxiosError): void;

  EDIT_PENDING(state: ILdapEntryState): void;
  EDIT_SUCCESS(state: ILdapEntryState, data: ILdapEntry): void;
  EDIT_ERROR(state: ILdapEntryState, error?: AxiosError): void;

  DELETE_PENDING(state: ILdapEntryState): void;
  DELETE_SUCCESS(state: ILdapEntryState, entryDn: string): void;
  DELETE_ERROR(state: ILdapEntryState, response: any): void;
}

export const LdapEntryMutationsDefault: ILdapEntryMutations = {
  GET_ALL_PENDING(state: ILdapEntryState): void {
    state.isLoading = true;
    state.error.status = null;
    state.clearList();
  },
  GET_ALL_SUCCESS(state: ILdapEntryState, data: IListResponse<ILdapEntry>): void {
    state.isLoading = false;
    state.error.status = null;
    state.items.push(...data.items);
    state.total = data.total;
  },
  GET_ALL_ERROR(state: ILdapEntryState, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  GET_PENDING(state: ILdapEntryState): void {
    state.isLoading = true;
    state.error.status = null;
  },
  GET_SUCCESS(state: ILdapEntryState, data: ILdapEntry = null): void {
    state.isLoading = false;
    state.error.status = null;
    state.item = data;
  },
  GET_ERROR(state: ILdapEntryState, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  CREATE_PENDING(state: ILdapEntryState): void {
    state.isLoading = true;
    state.error.status = null;
    state.error.message = null;
  },
  CREATE_SUCCESS(state: ILdapEntryState, data: ILdapEntry = null): void {
    state.isLoading = false;
    state.error.status = null;
    state.error.message = null;
    state.item = data;
  },
  CREATE_ERROR(state: ILdapEntryState, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.data = error.response.data;
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  EDIT_PENDING(state: ILdapEntryState): void {
    state.isLoading = true;
    state.error.status = null;
    state.error.message = null;
  },
  EDIT_SUCCESS(state: ILdapEntryState, data: ILdapEntry = null): void {
    state.isLoading = false;
    state.error.status = null;
    state.error.message = null;
    state.item = data;
  },
  EDIT_ERROR(state: ILdapEntryState, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.data = error.response.data;
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  DELETE_PENDING(state: ILdapEntryState): void {
    state.isLoading = true;
    state.error.status = null;
    state.error.message = null;
  },
  DELETE_SUCCESS(state: ILdapEntryState, entryDn: string): void {
    state.isLoading = false;
    state.error.status = null;
    state.error.message = null;
    state.items.splice(
      state.items.findIndex((i: ILdapEntry) => {
        return i.dn === entryDn;
      }),
      1
    );
  },
  DELETE_ERROR(state: ILdapEntryState, response: any): void {
    state.isLoading = false;
    if (response) {
      state.error.status = response.status;
    } else {
      state.error.status = 418;
    }
  }

};
