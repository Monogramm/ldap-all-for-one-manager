import { ActionContext } from "vuex";

import { IRootState } from "../../interfaces/state";
import { IReadWriteActions, ReadWriteActions } from "../../store/actions";

import { ILdapEntryState } from "./state";
import { ILdapEntry } from "./interfaces";

/**
 * LdapEntry API actions interface.
 */
export interface ILdapEntryActions extends IReadWriteActions<ILdapEntry, ILdapEntryState> {
}

export const LdapEntryActionsDefault: ILdapEntryActions = {
  ...ReadWriteActions,
};
