import { IError } from "../../interfaces/error";

import { ILdapEntry } from "./interfaces";
import { ILdapEntryState } from "./state";

export interface ILdapEntryGetters {
  isLoading(state: ILdapEntryState): boolean;
  hasError(state: ILdapEntryState): boolean;
  error(state: ILdapEntryState): IError;
  items(state: ILdapEntryState): Array<ILdapEntry>;
  item(state: ILdapEntryState): ILdapEntry;
  total(state: ILdapEntryState): number;
}

export const LdapEntryGettersDefault: ILdapEntryGetters = {
  isLoading(state: ILdapEntryState): boolean {
    return state.isLoading;
  },
  hasError(state: ILdapEntryState): boolean {
    return state.error.status !== null;
  },
  error(state: ILdapEntryState): IError {
    return state.error;
  },
  items(state: ILdapEntryState): Array<ILdapEntry> {
    return state.items;
  },
  item(state: ILdapEntryState): ILdapEntry {
    return state.item;
  },
  total(state: ILdapEntryState): number {
    return state.total;
  }
}
