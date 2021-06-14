import { IError, Error } from "../../interfaces/error";

import { ILdapEntry, LdapEntry } from "./interfaces";
import { LdapEntryAPI } from "./api";

/**
 * LdapEntry store state interface.
 */
export interface ILdapEntryState {
  api: LdapEntryAPI;

  error: IError;
  isLoading: boolean;
  total: number;
  items: Array<ILdapEntry>;
  item: ILdapEntry;

  initList(): Array<ILdapEntry>;
  initCurrent(): ILdapEntry;

  clearList(): void;
}

/**
 * LdapEntry store state class.
 */
export class LdapEntryState implements ILdapEntryState {
  api = LdapEntryAPI.Instance;

  error: IError = new Error();
  isLoading: boolean = false;
  total: number = 0;
  items: Array<ILdapEntry> = this.initList();
  item: ILdapEntry = this.initCurrent();

  initList(): Array<ILdapEntry> {
    return [];
  }

  initCurrent(): ILdapEntry {
    return new LdapEntry(null);
  }

  clearList(): void {
    this.items.splice(0, this.items.length);
  }
}

/**
 * Factory to generate new default LdapEntry store state class.
 */
export const LdapEntryStateDefault = (): LdapEntryState => {
  return new LdapEntryState();
};
