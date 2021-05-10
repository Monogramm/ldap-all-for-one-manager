import { AxiosError } from "axios";

import { IListResponse } from "../../api";
import { IReadWriteMutations, ReadWriteMutations } from "../../store/mutations";

import { ILdapEntryState } from "./state";
import { ILdapEntry } from "./interfaces";

export interface ILdapEntryMutations extends IReadWriteMutations<ILdapEntry, ILdapEntryState> {
}

export const LdapEntryMutationsDefault: ILdapEntryMutations = {
  ...ReadWriteMutations,
};
