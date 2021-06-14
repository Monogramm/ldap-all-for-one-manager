import { AxiosError } from "axios";
import { IEntityState } from "../interfaces/state";
import { IEntity } from "../interfaces/entity";
import { IListResponse } from "../api";

/**
 * Generic mutations interface.
 */
export interface IMutations {
}


/**
 * Generic mutations interface for regular Read Only operations.
 */
export interface IReadMutations<T extends IEntity = IEntity, S extends IEntityState<T> = IEntityState<T>> extends IMutations {
  GET_ALL_PENDING(state: S): void;
  GET_ALL_SUCCESS(state: S, data: IListResponse<T>): void;
  GET_ALL_ERROR(state: S, error?: AxiosError): void;

  GET_PENDING(state: S): void;
  GET_SUCCESS(state: S, data: T): void;
  GET_ERROR(state: S, error?: AxiosError): void;
}


/**
 * Generic state mutations object for regular Read Only operations.
 */
export const ReadMutations: IReadMutations<IEntity, IEntityState<IEntity>> = {
  GET_ALL_PENDING(state: IEntityState<IEntity>): void {
    state.isLoading = true;
    state.error.status = null;
    state.clearList();
  },
  GET_ALL_SUCCESS(state: IEntityState<IEntity>, data: IListResponse<IEntity>): void {
    state.isLoading = false;
    state.error.status = null;
    state.items.push(...data.items);
    state.total = data.total;
  },
  GET_ALL_ERROR(state: IEntityState<IEntity>, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  GET_PENDING(state: IEntityState<IEntity>): void {
    state.isLoading = true;
    state.error.status = null;
  },
  GET_SUCCESS(state: IEntityState<IEntity>, data: IEntity = null): void {
    state.isLoading = false;
    state.error.status = null;
    state.item = data;
  },
  GET_ERROR(state: IEntityState<IEntity>, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  }

};



/**
 * Generic mutations interface for regular CRUD operations.
 */
export interface IReadWriteMutations<T extends IEntity = IEntity, S extends IEntityState<T> = IEntityState<T>> extends IReadMutations<T, S> {
  CREATE_PENDING(state: S): void;
  CREATE_SUCCESS(state: S, data: T): void;
  CREATE_ERROR(state: S, error?: AxiosError): void;

  EDIT_PENDING(state: S): void;
  EDIT_SUCCESS(state: S, data: T): void;
  EDIT_ERROR(state: S, error?: AxiosError): void;

  DELETE_PENDING(state: S): void;
  DELETE_SUCCESS(state: S, entityId: string): void;
  DELETE_ERROR(state: S, response: any): void;
}

/**
 * Generic state mutations object for regular CRUD operations.
 */
export const ReadWriteMutations: IReadWriteMutations<IEntity, IEntityState<IEntity>> = {
  ...ReadMutations,

  CREATE_PENDING(state: IEntityState<IEntity>): void {
    state.isLoading = true;
    state.error.status = null;
    state.error.message = null;
  },
  CREATE_SUCCESS(state: IEntityState<IEntity>, data: IEntity = null): void {
    state.isLoading = false;
    state.error.status = null;
    state.error.message = null;
    state.item = data;
  },
  CREATE_ERROR(state: IEntityState<IEntity>, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  EDIT_PENDING(state: IEntityState<IEntity>): void {
    state.isLoading = true;
    state.error.status = null;
    state.error.message = null;
  },
  EDIT_SUCCESS(state: IEntityState<IEntity>, data: IEntity = null): void {
    state.isLoading = false;
    state.error.status = null;
    state.error.message = null;
    state.item = data;
  },
  EDIT_ERROR(state: IEntityState<IEntity>, error?: AxiosError): void {
    state.isLoading = false;
    if (error && error.response) {
      state.error.status = error.response.status;
      state.error.message = error.response.statusText;
    } else {
      state.error.status = 418;
      state.error.message = 'unknown-error';
    }
  },

  DELETE_PENDING(state: IEntityState<IEntity>): void {
    state.isLoading = true;
    state.error.status = null;
    state.error.message = null;
  },
  DELETE_SUCCESS(state: IEntityState<IEntity>, entityId: string): void {
    state.isLoading = false;
    state.error.status = null;
    state.error.message = null;
    state.items.splice(
      state.items.findIndex((i: IEntity) => {
        return i.id === entityId;
      }),
      1
    );
  },
  DELETE_ERROR(state: IEntityState<IEntity>, response: any): void {
    state.isLoading = false;
    state.error.status = response.status;
    if (response) {
      state.error.status = response.status;
    } else {
      state.error.status = 418;
    }
  }

};
