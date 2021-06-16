/**
 * Generic entity interface with standard metadata.
 */
export interface IEntity {
  id: string;
  createdAt: Date;
  updatedAt: Date;
}

/**
 * Abstract generic entity class with standard metadata.
 */
export abstract class Entity implements IEntity {
  constructor(
      public id: string = null,
      public createdAt: Date = new Date(),
      public updatedAt: Date = new Date(),
  ) {}
}
