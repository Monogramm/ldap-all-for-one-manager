/**
 * Error interface.
 */
export interface IError {
  code: number;
  data: string;
  message: string;
  status: number;
}

/**
 * Error class.
 */
export class Error implements IError {
  code: number = null;
  data: string = null;
  message: string = null;
  status: number = null;
}
