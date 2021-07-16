import { AxiosError } from "axios";
import { Module, ActionContext } from "vuex";

import { IRootState } from "@/vue/interfaces/state";

import { ILdapEntry } from "./interfaces";

import { ILdapEntryState, LdapEntryStateDefault } from "./state";
import { LdapEntryGettersDefault } from "./getters";
import { LdapEntryMutationsDefault } from "./mutations";
import { LdapEntryActionsDefault } from "./actions";

export const LdapEntryModule: Module<ILdapEntryState, IRootState> = {
  namespaced: true,
  state: LdapEntryStateDefault,
  getters: {
    ...LdapEntryGettersDefault,
  },
  mutations: {
    ...LdapEntryMutationsDefault,
  },
  actions: {
    ...LdapEntryActionsDefault,
  },
};

export default LdapEntryModule;
