const greaterThanZero = (value: number) => value > 0;

const isBoolean = (value: never) => typeof value == "boolean";

const orderReferenceNumber = (value: string) => /^[a-z0-9\-_]+$/i.test(value);

const ticketId = (value: string) => /^[a-z0-9\-_.]+$/i.test(value);

export { greaterThanZero, isBoolean, orderReferenceNumber, ticketId };
