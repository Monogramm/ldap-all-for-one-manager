import { IEntity, Entity } from "../../../interfaces/entity";

/**
 * LDAP Entry attributes interface.
 */
export interface ILdapAttributes {
  [attribute: string]: Array<any>;
}

/**
 * LDAP Entry interface.
 */
export interface ILdapEntry extends IEntity {
  dn: string;
  attributes: ILdapAttributes;

  /**
   * Returns a specific attribute's value.
   *
   * As LDAP can return multiple values for a single attribute,
   * this value is returned as an array.
   *
   * @param string name The name of the attribute
   *
   * @return Array of attribute values
   */
  getAttribute(name: string): Array<any>;

  /**
   * Sets a value for the given attribute.
   *
   * @param string name The name of the attribute
   */
  setAttribute(name: string, value: Array<any>): void;

  /**
   * Removes a given attribute.
   *
   * @param string name The name of the attribute
   */
  removeAttribute(name: string): void;
}

/**
 * LDAP Entry class.
 */
export class LdapEntry extends Entity implements ILdapEntry {
  constructor(
    dn: string,
    createdAt: Date = new Date(),
    updatedAt: Date = new Date(),
    public attributes: ILdapAttributes = null,
  ) {
    super(dn, createdAt, updatedAt);
  }

  get dn(): string {
    return this.id;
  }

  set dn(dn: string) {
    this.id = dn;
  }

  getAttribute(name: string): Array<any> {
    return this.attributes[name] ?? null;
  }

  setAttribute(name: string, value: Array<any>): void {
    this.attributes[name] = value;
  }

  removeAttribute(name: string): void {
    delete this.attributes[name];
  }
}

/**
 * Factory to generate new default LdapEntry class.
 */
export const LdapEntryDefault = (): LdapEntry => {
  return new LdapEntry("uid=");
};