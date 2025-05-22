type Attribute {
  id: String!
  displayValue: String!
  value: String!
}

type Query {
  attributes: [Attribute!]!
}

type Mutation {
  createAttribute(attribute: AttributeInput!): Attribute!
}
