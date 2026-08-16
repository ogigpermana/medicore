# SDLC Methodology & Phases

**Project:** MediCore - Pharmacy Management System  
**Version:** 1.0  
**Date:** August 16, 2026

## 1. SDLC Methodology Selection

**Methodology:** Hybrid Agile-Waterfall  
**Rationale:** 
- **Agile** untuk development (flexibility, iterative delivery)
- **Waterfall elements** untuk planning & compliance (pharmacy systems need proper documentation)
- **Scrum framework** untuk sprint management

## 2. SDLC Phases

### Phase 1: Requirements & Planning (Week 1-2)

**Activities:**
- Stakeholder interviews (simulated untuk portfolio)
- Requirements gathering (PRD completion)
- Technical feasibility analysis
- Risk assessment
- Resource planning
- Timeline finalization

**Deliverables:**
- Approved PRD
- Technical architecture document
- Risk register
- Project timeline

**Success Criteria:**
- All requirements documented
- Technical feasibility confirmed
- Timeline realistic
- Risks identified and mitigated

### Phase 2: Design (Week 3)

**Activities:**
- System architecture design
- Database schema design
- API design (RESTful endpoints)
- UI/UX wireframes
- Security architecture
- Infrastructure planning

**Deliverables:**
- System architecture diagram
- Database ERD
- API documentation (Swagger/OpenAPI)
- UI/UX mockups
- Security design document

**Success Criteria:**
- Architecture approved
- Database schema normalized
- API endpoints defined
- UI/UX validated
- Security threats identified

### Phase 3: Development Sprint 1-4 (Week 4-11)

#### Sprint 1 (Week 4-5): Foundation
- Custom PHP framework setup
- Database setup & migrations
- Authentication system
- Basic UI layout

#### Sprint 2 (Week 6-7): Core Features
- Inventory management module
- CRUD operations
- Basic reporting

#### Sprint 3 (Week 8-9): POS Module
- Shopping cart
- Barcode scanning
- Payment processing
- Receipt generation

#### Sprint 4 (Week 10-11): Advanced Features
- Prescription management
- Customer management
- Advanced reporting
- Notifications/alerts

**Deliverables:**
- Working software increment
- Unit tests
- Integration tests
- Documentation updates

**Success Criteria:**
- All sprint stories completed
- Tests passing
- Code reviewed
- Documentation updated

### Phase 4: Testing (Week 12)

**Activities:**
- Unit testing
- Integration testing
- System testing
- Security testing
- Performance testing
- User acceptance testing (UAT)

**Deliverables:**
- Test reports
- Bug fixes
- UAT sign-off

**Success Criteria:**
- All critical bugs resolved
- Security audit passed
- Performance benchmarks met
- UAT signed off
- Test coverage > 70%

### Phase 5: Deployment (Week 13)

**Activities:**
- Production server setup
- Database migration
- Application deployment
- Monitoring setup
- Backup configuration
- Go-live preparation

**Deliverables:**
- Deployed application
- Deployment documentation
- Monitoring dashboard

**Success Criteria:**
- Application deployed
- Database migrated
- Monitoring active
- Backup system configured
- Go-live completed

### Phase 6: Maintenance & Support (Ongoing)

**Activities:**
- Bug fixes
- Performance optimization
- Feature enhancements
- Security updates
- User support

**Deliverables:**
- Regular updates
- Bug reports
- Performance metrics
- Security patches

## 3. Quality Gates

### Gate 1: Requirements Sign-off (End of Phase 1)
- PRD approved
- Stakeholder sign-off
- Timeline confirmed

### Gate 2: Design Approval (End of Phase 2)
- Architecture approved
- Database schema finalized
- API documentation complete
- UI/UX validated

### Gate 3: Sprint Reviews (End of Each Sprint)
- Sprint goals achieved
- Stories completed
- Tests passing
- Demo conducted

### Gate 4: Testing Completion (End of Phase 4)
- All tests passed
- Security audit cleared
- Performance benchmarks met
- UAT signed off

### Gate 5: Production Readiness (End of Phase 5)
- Deployment successful
- Monitoring active
- Backup configured
- Documentation complete

## 4. Risk Management

### Risk Register

| Risk | Probability | Impact | Mitigation Strategy | Owner |
|------|-------------|--------|-------------------|-------|
| Scope creep | Medium | High | Change control process | Product Owner |
| Technical debt | High | Medium | Regular refactoring | Development Team |
| Performance issues | Medium | High | Load testing, optimization | Development Team |
| Security vulnerabilities | Low | Critical | Security audits, best practices | Development Team |
| Timeline delays | Medium | Medium | Buffer time, prioritization | Scrum Master |

## 5. Communication Plan

### Daily Stand-up
- **Time:** 9:00 AM daily
- **Duration:** 15 minutes
- **Participants:** Development team
- **Format:** What did I do? What will I do? Any impediments?

### Sprint Review
- **Time:** End of each sprint
- **Duration:** 1 hour
- **Participants:** Team, stakeholders
- **Agenda:** Demo, feedback, backlog updates

### Sprint Retrospective
- **Time:** End of each sprint
- **Duration:** 1 hour
- **Participants:** Team only
- **Agenda:** What went well? What didn't? Improvements?

### Backlog Refinement
- **Time:** Mid-sprint
- **Duration:** 1 hour
- **Participants:** Team, Product Owner
- **Agenda:** Review upcoming stories, estimation

## 6. Documentation Requirements

### Technical Documentation
- Architecture diagrams
- Database schema
- API documentation
- Deployment guides
- Troubleshooting guides

### User Documentation
- User manuals
- Training materials
- FAQ
- Quick start guides

### Process Documentation
- SDLC process
- Scrum guidelines
- Quality standards
- Coding standards

## 7. Success Metrics

### Process Metrics
- Sprint completion rate
- Velocity stability
- Bug detection rate
- Test coverage

### Product Metrics
- User satisfaction
- System uptime
- Response time
- Error rate

### Portfolio Metrics
- Code quality
- Documentation completeness
- Architecture soundness
- Security implementation

---

**Document Status:** Approved  
**Next Phase:** System Design