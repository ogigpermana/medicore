# Agile Scrum Framework

**Project:** MediCore - Pharmacy Management System  
**Version:** 1.0  
**Sprint Duration:** 2 weeks  
**Date:** August 16, 2026

## 1. Scrum Team Structure

### Team Composition (Solo Dev Portfolio Mode)

| Role | Responsibility | Assigned To |
|------|---------------|-------------|
| **Product Owner** | Define/prioritize backlog, accept deliverables | You (stakeholder) |
| **Scrum Master** | Facilitate events, remove impediments | You (process facilitator) |
| **Development Team** | Deliver shippable increment, estimate stories | You (full-stack dev) |
| **Stakeholders** | Provide feedback, validate requirements | Simulated users |

## 2. Sprint Planning

### Sprint Configuration
- **Duration:** 2 weeks
- **Sprint 1:** Week 4-5 (Foundation)
- **Sprint 2:** Week 6-7 (Inventory)
- **Sprint 3:** Week 8-9 (POS)
- **Sprint 4:** Week 10-11 (Advanced Features)

### Sprint Capacity Calculation
- Available hours: 10 days × 6 hours = 60 hours
- Buffer (20%): 12 hours
- Effective capacity: 48 hours per sprint

### Sprint Planning Meeting (2 hours)
**Agenda:**
1. Review sprint goal
2. Select backlog items for sprint
3. Break down stories into tasks
4. Estimate effort (story points)
5. Confirm capacity

## 3. Product Backlog

### Epic 1: Authentication & Authorization
**Priority:** P0 (Critical)  
**Story Points:** 21

| ID | User Story | Priority | Story Points |
|----|------------|----------|--------------|
| US-001 | As a user, I want to register with email verification so that I can access the system securely | P0 | 5 |
| US-002 | As a user, I want to login with email/password so that I can access my account | P0 | 3 |
| US-003 | As an admin, I want to manage user roles so that I can control access permissions | P0 | 8 |
| US-004 | As a user, I want to reset my password via email so that I can regain access | P1 | 5 |

### Epic 2: Inventory Management
**Priority:** P0 (Critical)  
**Story Points:** 55

| ID | User Story | Priority | Story Points |
|----|------------|----------|--------------|
| US-005 | As a pharmacist, I want to add new products so that I can maintain inventory | P0 | 5 |
| US-006 | As a pharmacist, I want to edit product details so that I can keep information accurate | P0 | 3 |
| US-007 | As a pharmacist, I want to delete products so that I can remove obsolete items | P1 | 3 |
| US-008 | As a pharmacist, I want to adjust stock levels so that I can reflect actual inventory | P0 | 5 |
| US-009 | As a pharmacist, I want to manage suppliers so that I can track product sources | P1 | 8 |
| US-010 | As a pharmacist, I want to generate barcodes so that I can scan products quickly | P1 | 8 |
| US-011 | As a pharmacist, I want to receive low stock alerts so that I can reorder in time | P0 | 5 |
| US-012 | As a pharmacist, I want to receive expiry alerts so that I can prevent losses | P0 | 5 |
| US-013 | As a pharmacist, I want to view stock reports so that I can analyze inventory | P1 | 8 |
| US-014 | As a pharmacist, I want to manage product categories so that I can organize items | P2 | 5 |

### Epic 3: Point of Sale
**Priority:** P0 (Critical)  
**Story Points:** 40

| ID | User Story | Priority | Story Points |
|----|------------|----------|--------------|
| US-015 | As a cashier, I want to search products quickly so that I can serve customers fast | P0 | 5 |
| US-016 | As a cashier, I want to scan barcodes so that I can add products to cart efficiently | P0 | 8 |
| US-017 | As a cashier, I want to manage shopping cart so that I can process multiple items | P0 | 8 |
| US-018 | As a cashier, I want to apply discounts so that I can offer promotions | P1 | 5 |
| US-019 | As a cashier, I want to process different payment methods so that I can accept various payments | P0 | 8 |
| US-020 | As a cashier, I want to generate receipts so that customers have proof of purchase | P0 | 5 |
| US-021 | As a cashier, I want to void transactions so that I can correct mistakes | P1 | 3 |

### Epic 4: Prescription Management
**Priority:** P1 (High)  
**Story Points:** 35

| ID | User Story | Priority | Story Points |
|----|------------|----------|--------------|
| US-022 | As a pharmacist, I want to create prescription records so that I can track doctor orders | P1 | 8 |
| US-023 | As a pharmacist, I want to upload prescription images so that I can verify authenticity | P1 | 5 |
| US-024 | As a pharmacist, I want to validate prescriptions so that I can ensure patient safety | P0 | 8 |
| US-025 | As a pharmacist, I want to dispense prescriptions so that I can fulfill doctor orders | P1 | 8 |
| US-026 | As a pharmacist, I want to view prescription history so that I can track patient records | P2 | 5 |

### Epic 5: Customer Management
**Priority:** P2 (Medium)  
**Story Points:** 20

| ID | User Story | Priority | Story Points |
|----|------------|----------|--------------|
| US-027 | As a cashier, I want to register customers so that I can track their purchases | P2 | 5 |
| US-028 | As a cashier, I want to view customer history so that I can provide better service | P2 | 5 |
| US-029 | As an owner, I want to manage customer segments so that I can target marketing | P3 | 5 |
| US-030 | As a customer, I want to view my purchase history so that I can track my spending | P3 | 5 |

### Epic 6: Reporting & Analytics
**Priority:** P1 (High)  
**Story Points:** 40

| ID | User Story | Priority | Story Points |
|----|------------|----------|--------------|
| US-031 | As an owner, I want to view daily sales reports so that I can track performance | P1 | 5 |
| US-032 | As an owner, I want to view monthly sales reports so that I can analyze trends | P1 | 5 |
| US-033 | As an owner, I want to view profit/loss reports so that I can understand financial health | P1 | 8 |
| US-034 | As an owner, I want to view product performance so that I can optimize inventory | P1 | 8 |
| US-035 | As an owner, I want to export reports to PDF so that I can share with stakeholders | P2 | 5 |
| US-036 | As an owner, I want to export reports to Excel so that I can do further analysis | P2 | 5 |

## 4. Sprint Backlogs

### Sprint 1 Backlog (Foundation)
**Sprint Goal:** Establish development foundation with working authentication system  
**Capacity:** 48 hours  
**Total Story Points:** 21

| Story | Tasks | Hours | Status |
|-------|-------|-------|--------|
| US-001 | - Create registration form<br>- Implement email verification<br>- Add validation | 12 | To Do |
| US-002 | - Create login form<br>- Implement JWT authentication<br>- Add session management | 8 | To Do |
| US-003 | - Design role database schema<br>- Implement RBAC middleware<br>- Create role management UI | 16 | To Do |
| US-004 | - Implement password reset email<br>- Create reset form<br>- Add token validation | 12 | To Do |

### Sprint 2 Backlog (Inventory)
**Sprint Goal:** Deliver functional inventory management system  
**Capacity:** 48 hours  
**Total Story Points:** 40 (selected from 55)

| Story | Tasks | Hours | Status |
|-------|-------|-------|--------|
| US-005 | - Design product database schema<br>- Create product CRUD API<br>- Build product management UI | 12 | To Do |
| US-006 | - Implement edit functionality<br>- Add validation<br>- Update UI | 6 | To Do |
| US-008 | - Create stock adjustment logic<br>- Build stock update UI<br>- Add audit logging | 10 | To Do |
| US-011 | - Implement low stock check<br>- Create notification system<br>- Add alert UI | 8 | To Do |
| US-012 | - Implement expiry check<br>- Create expiry alert logic<br>- Add alert UI | 8 | To Do |
| US-013 | - Create stock report queries<br>- Build report UI<br>- Add export functionality | 4 | To Do |

### Sprint 3 Backlog (POS)
**Sprint Goal:** Deliver complete point of sale system  
**Capacity:** 48 hours  
**Total Story Points:** 38 (selected from 40)

| Story | Tasks | Hours | Status |
|-------|-------|-------|--------|
| US-015 | - Implement product search API<br>- Create search UI with autocomplete<br>- Add search optimization | 8 | To Do |
| US-016 | - Integrate barcode scanner library<br>- Implement barcode lookup<br>- Add scanner UI | 12 | To Do |
| US-017 | - Create cart data structure<br>- Implement cart operations<br>- Build cart UI | 10 | To Do |
| US-019 | - Implement payment logic<br>- Add payment method validation<br>- Create payment UI | 10 | To Do |
| US-020 | - Generate receipt template<br>- Implement PDF generation<br>- Add print functionality | 8 | To Do |

### Sprint 4 Backlog (Advanced Features)
**Sprint Goal:** Complete advanced features and system polish  
**Capacity:** 48 hours  
**Total Story Points:** 40 (selected from multiple epics)

| Story | Tasks | Hours | Status |
|-------|-------|-------|--------|
| US-022 | - Design prescription schema<br>- Create prescription CRUD<br>- Build prescription UI | 10 | To Do |
| US-024 | - Implement validation logic<br>- Add drug interaction checks<br>- Create validation UI | 8 | To Do |
| US-027 | - Create customer CRUD<br>- Build customer management UI<br>- Add customer search | 8 | To Do |
| US-031 | - Create sales report queries<br>- Build dashboard UI<br>- Add charts | 8 | To Do |
| US-033 | - Implement profit calculation<br>- Create financial reports<br>- Add report filters | 8 | To Do |
| US-035 | - Implement PDF export<br>- Add export UI<br>- Test export functionality | 6 | To Do |

## 5. Scrum Events (Ceremonies)

### Daily Stand-up (Daily Scrum)
**Duration:** 15 minutes  
**Time:** 9:00 AM daily  
**Format:** 
- What did I complete yesterday?
- What will I do today?
- What impediments do I have?

**Tools:** Trello/GitHub Projects (simulated)

### Sprint Review
**Duration:** 1 hour  
**Participants:** PO, SM, Development Team, Stakeholders  
**Agenda:**
- Demo completed features
- Collect feedback
- Update product backlog

### Sprint Retrospective
**Duration:** 1 hour  
**Participants:** Scrum Team only  
**Agenda:**
- What went well?
- What didn't go well?
- What improvements can we make?
- Action items for next sprint

### Backlog Refinement
**Duration:** 1 hour (mid-sprint)  
**Activities:**
- Review upcoming stories
- Clarify acceptance criteria
- Estimate effort
- Prioritize backlog

## 6. Definition of Done (DoD)

Each User Story must meet:
- [ ] Code completed and reviewed
- [ ] Unit tests written (>70% coverage)
- [ ] Integration tests passed
- [ ] Code documented
- [ ] Security review passed
- [ ] Performance acceptable
- [ ] UI/UX approved
- [ ] Product Owner acceptance
- [ ] Deployed to staging

## 7. Definition of Ready (DoR)

User Story must be:
- [ ] Clearly defined with acceptance criteria
- [ ] Estimated by development team
- [ ] Dependencies identified
- [ ] Design completed (if applicable)
- [ ] Priority assigned
- [ ] Sized appropriately (< 13 story points)

## 8. Story Point Estimation

**Scale:** Fibonacci (1, 2, 3, 5, 8, 13, 21)  
**Benchmarks:**
- 1 point: Simple task (< 2 hours)
- 2 points: Easy task (2-4 hours)
- 3 points: Moderate task (4-8 hours)
- 5 points: Complex task (8-12 hours)
- 8 points: Very complex (12-20 hours)
- 13 points: Extremely complex (20-30 hours)
- 21 points: Epic-level (> 30 hours)

## 9. Velocity Tracking

**Target Velocity:** 20-25 story points per sprint  
**Current Velocity:** TBD (after Sprint 1)  
**Velocity Stability:** ±5 points variance acceptable

## 10. Impediment Management

**Process:**
1. Identify impediment during daily stand-up
2. Log impediment in tracker
3. Scrum Master facilitates resolution
4. Remove impediment or escalate
5. Communicate resolution to team

---

**Document Status:** Approved  
**Next Phase:** Sprint 1 Planning