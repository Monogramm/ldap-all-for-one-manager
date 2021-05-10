import { IGetters, BaseGetters } from "../../store/getters";
import { ILdapEntry } from "./interfaces";
import { ILdapEntryState } from "./state";

// XXX IGetters<ILdapEntry, ILdapEntryState>
export interface ILdapEntryGetters extends IGetters {}

export const LdapEntryGettersDefault: ILdapEntryGetters = {
  ...BaseGetters
}
