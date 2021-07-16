import Vue from "vue";
import Vuex, { Module, Store } from 'vuex';
import merge from 'deepmerge';

import { IState, IRootState } from "../interfaces/state";

import { AuthModule } from "../modules/auth/module";
import { UserModule } from "../modules/user/module";
import { SupportModule } from "../modules/support/module";
import { ParameterModule } from "../modules/parameter/module";
import { BackgroundJobModule } from "../modules/backgroundJob/module";
import { CurrencyModule } from "../modules/currency/module";
import { MediaModule } from "../modules/media/module";
import { LdapEntryModule } from "../modules/ldap/module";

Vue.use(Vuex);

const state: IState = null;

export const store: Store<IRootState> = new Vuex.Store({
  modules: {
    auth: AuthModule,
    user: UserModule,
    support: SupportModule,
    parameter: ParameterModule,
    backgroundJob: BackgroundJobModule,
    currency: CurrencyModule,
    media: MediaModule,
    ldapEntry: LdapEntryModule,
  },
});

export default store;

//export const registerModule = (moduleName: string, module: Module<any, any>) => {
//  const moduleIsRegistered: boolean = (store as any)._modules.root._children[moduleName] !== undefined;
//  const stateExists: boolean = store.state[moduleName] !== undefined;
//
//  if (stateExists) {
//    module.state = merge(module.state, store.state[moduleName], {
//      clone: false,
//      arrayMerge: /* istanbul ignore next */ (target: any, source: any) => {
//        return source;
//      },
//    });
//  }
//
//  if (!moduleIsRegistered) {
//    store.registerModule(moduleName, module, { preserveState: false });
//  }
//};
//
//registerModule('auth', AuthModule);

