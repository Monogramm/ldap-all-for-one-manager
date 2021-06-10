import { IEntityApiState, AbstractEntityState } from "../../interfaces/state";
import { ILdapEntry, LdapEntry } from "./interfaces";
import { LdapEntryAPI } from "./api";

/**
 * LdapEntry store state interface.
 */
export interface ILdapEntryState extends IEntityApiState<ILdapEntry, LdapEntryAPI> {}

/**
 * LdapEntry store state class.
 */
export class LdapEntryState extends AbstractEntityState<ILdapEntry>
  implements ILdapEntryState {
  api = LdapEntryAPI.Instance;

  initCurrent(): ILdapEntry {
    return new LdapEntry(null,null);
  }
}

/**
 * Factory to generate new default LdapEntry store state class.
 */
export const LdapEntryStateDefault = (): LdapEntryState => {
  return new LdapEntryState();
};
