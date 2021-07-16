/**
 * LDAP Entry attributes interface.
 */
export interface ILdapAttributes {
  [attribute: string]: Array<any>;
}

/**
 * LDAP Entry interface.
 */
export interface ILdapEntry {
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
export class LdapEntry implements ILdapEntry {
  constructor(
    public dn : string,
    public attributes: ILdapAttributes = {},
  ) { }

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
  return new LdapEntry(null);
};
