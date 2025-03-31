import { Camera, Edit, FileText, GraduationCap, Mail, Phone, Save, User } from "lucide-react"
import Image from "next/image"
import Link from "next/link"

import { Button } from "@/components/ui/button"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Input } from "@/components/ui/input"
import { Label } from "@/components/ui/label"
import { Separator } from "@/components/ui/separator"
import { Sheet, SheetContent, SheetTrigger } from "@/components/ui/sheet"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { Textarea } from "@/components/ui/textarea"

export default function StudentProfile() {
  // Mock student data - in a real app, this would come from an API or database
  const student = {
    id: "ST12345",
    name: "Alex Johnson",
    email: "alex.johnson@university.edu",
    phone: "(555) 123-4567",
    dateOfBirth: "1998-05-15",
    address: "123 Campus Drive, University City, State 12345",
    program: "Bachelor of Science in Computer Science",
    enrollmentYear: "2022",
    expectedGraduation: "2026",
    currentSemester: "Spring 2025",
    gpa: "3.75",
    credits: "45",
    advisor: "Dr. Sarah Williams",
    bio: "Computer Science student with interests in artificial intelligence and web development. Active member of the Coding Club and Robotics Team.",
    documents: [
      { id: 1, name: "Transcript_Spring2025.pdf", date: "Apr 15, 2025", size: "1.2 MB" },
      { id: 2, name: "Financial_Aid_Form.pdf", date: "Mar 10, 2025", size: "850 KB" },
      { id: 3, name: "Course_Registration.pdf", date: "Jan 5, 2025", size: "1.5 MB" },
    ],
  }

  return (
    <div className="min-h-screen flex flex-col bg-gray-50">
      {/* Navigation - Mobile */}
      <div className="md:hidden flex items-center justify-between p-4 bg-white border-b">
        <div className="flex items-center gap-2">
          <Image
            src="/placeholder.svg?height=40&width=40"
            alt="University Logo"
            width={40}
            height={40}
            className="rounded"
          />
          <span className="font-semibold text-lg">Student Portal</span>
        </div>
        <Sheet>
          <SheetTrigger asChild>
            <Button variant="ghost" size="icon">
              <User className="h-6 w-6" />
              <span className="sr-only">Open menu</span>
            </Button>
          </SheetTrigger>
          <SheetContent side="right">
            <nav className="flex flex-col gap-4 mt-8">
              <Link href="/" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <GraduationCap className="h-5 w-5" />
                <span>Dashboard</span>
              </Link>
              <Link href="/profile" className="flex items-center gap-2 p-2 bg-gray-100 rounded-md">
                <User className="h-5 w-5" />
                <span>Profile</span>
              </Link>
              <Link href="#" className="flex items-center gap-2 p-2 hover:bg-gray-100 rounded-md">
                <FileText className="h-5 w-5" />
                <span>Courses</span>
              </Link>
            </nav>
          </SheetContent>
        </Sheet>
      </div>

      {/* Navigation - Desktop */}
      <div className="hidden md:flex items-center justify-between p-4 bg-white border-b">
        <div className="flex items-center gap-3">
          <Image
            src="/placeholder.svg?height=48&width=48"
            alt="University Logo"
            width={48}
            height={48}
            className="rounded"
          />
          <span className="font-semibold text-xl">Student Portal</span>
        </div>
        <nav className="flex items-center gap-6">
          <Link href="/" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <GraduationCap className="h-4 w-4" />
            <span>Dashboard</span>
          </Link>
          <Link href="/profile" className="text-primary font-medium flex items-center gap-1">
            <User className="h-4 w-4" />
            <span>Profile</span>
          </Link>
          <Link href="#" className="text-gray-700 hover:text-primary flex items-center gap-1">
            <FileText className="h-4 w-4" />
            <span>Courses</span>
          </Link>
        </nav>
      </div>

      {/* Main Content */}
      <main className="flex-1 p-4 md:p-8">
        <div className="max-w-5xl mx-auto space-y-8">
          {/* Profile Header */}
          <div className="bg-white rounded-lg shadow-sm p-6 md:p-8">
            <div className="flex flex-col md:flex-row gap-6 items-center md:items-start">
              <div className="relative">
                <div className="w-32 h-32 rounded-full bg-gray-200 overflow-hidden">
                  <Image
                    src="/placeholder.svg?height=128&width=128"
                    alt="Profile Picture"
                    width={128}
                    height={128}
                    className="object-cover"
                  />
                </div>
                <Button size="icon" variant="secondary" className="absolute bottom-0 right-0 rounded-full">
                  <Camera className="h-4 w-4" />
                  <span className="sr-only">Change profile picture</span>
                </Button>
              </div>
              <div className="flex-1 text-center md:text-left">
                <h1 className="text-2xl font-bold">{student.name}</h1>
                <p className="text-gray-500">
                  {student.id} • {student.program}
                </p>
                <div className="flex flex-wrap gap-3 mt-4 justify-center md:justify-start">
                  <div className="flex items-center gap-1 text-sm text-gray-600">
                    <Mail className="h-4 w-4" />
                    <span>{student.email}</span>
                  </div>
                  <div className="flex items-center gap-1 text-sm text-gray-600">
                    <Phone className="h-4 w-4" />
                    <span>{student.phone}</span>
                  </div>
                </div>
              </div>
              <Button variant="outline" size="sm" className="flex gap-1">
                <Edit className="h-4 w-4" />
                <span>Edit Profile</span>
              </Button>
            </div>
          </div>

          {/* Profile Content */}
          <Tabs defaultValue="personal" className="w-full">
            <TabsList className="grid grid-cols-3 md:w-[400px]">
              <TabsTrigger value="personal">Personal</TabsTrigger>
              <TabsTrigger value="academic">Academic</TabsTrigger>
              <TabsTrigger value="documents">Documents</TabsTrigger>
            </TabsList>

            {/* Personal Information Tab */}
            <TabsContent value="personal" className="mt-6 space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle>Personal Information</CardTitle>
                  <CardDescription>Your personal details and contact information</CardDescription>
                </CardHeader>
                <CardContent className="space-y-6">
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-2">
                      <Label htmlFor="fullName">Full Name</Label>
                      <Input id="fullName" defaultValue={student.name} />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="email">Email Address</Label>
                      <Input id="email" type="email" defaultValue={student.email} />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="phone">Phone Number</Label>
                      <Input id="phone" defaultValue={student.phone} />
                    </div>
                    <div className="space-y-2">
                      <Label htmlFor="dob">Date of Birth</Label>
                      <Input id="dob" type="date" defaultValue={student.dateOfBirth} />
                    </div>
                  </div>

                  <Separator />

                  <div className="space-y-2">
                    <Label htmlFor="address">Address</Label>
                    <Textarea id="address" defaultValue={student.address} rows={2} />
                  </div>

                  <div className="space-y-2">
                    <Label htmlFor="bio">Bio</Label>
                    <Textarea id="bio" defaultValue={student.bio} rows={4} placeholder="Tell us about yourself..." />
                  </div>

                  <div className="flex justify-end">
                    <Button className="flex gap-1">
                      <Save className="h-4 w-4" />
                      <span>Save Changes</span>
                    </Button>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            {/* Academic Information Tab */}
            <TabsContent value="academic" className="mt-6 space-y-6">
              <Card>
                <CardHeader>
                  <CardTitle>Academic Information</CardTitle>
                  <CardDescription>Your academic records and progress</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div className="space-y-4">
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">Program</h3>
                        <p>{student.program}</p>
                      </div>
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">Enrollment Year</h3>
                        <p>{student.enrollmentYear}</p>
                      </div>
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">Expected Graduation</h3>
                        <p>{student.expectedGraduation}</p>
                      </div>
                    </div>
                    <div className="space-y-4">
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">Current Semester</h3>
                        <p>{student.currentSemester}</p>
                      </div>
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">GPA</h3>
                        <p>{student.gpa}</p>
                      </div>
                      <div>
                        <h3 className="text-sm font-medium text-gray-500">Credits Completed</h3>
                        <p>{student.credits}</p>
                      </div>
                    </div>
                  </div>

                  <Separator className="my-6" />

                  <div>
                    <h3 className="text-sm font-medium text-gray-500 mb-2">Academic Advisor</h3>
                    <div className="flex items-center gap-4">
                      <div className="w-12 h-12 rounded-full bg-gray-200 flex items-center justify-center">
                        <User className="h-6 w-6 text-gray-500" />
                      </div>
                      <div>
                        <p className="font-medium">{student.advisor}</p>
                        <p className="text-sm text-gray-500">advisor@university.edu</p>
                      </div>
                      <Button variant="outline" size="sm" className="ml-auto">
                        Contact
                      </Button>
                    </div>
                  </div>
                </CardContent>
              </Card>

              <Card>
                <CardHeader>
                  <CardTitle>Academic Progress</CardTitle>
                  <CardDescription>Your progress toward graduation</CardDescription>
                </CardHeader>
                <CardContent>
                  <div className="space-y-6">
                    <div className="space-y-2">
                      <div className="flex justify-between text-sm">
                        <span>Credits Completed</span>
                        <span className="font-medium">{student.credits}/120</span>
                      </div>
                      <div className="w-full bg-gray-200 rounded-full h-2">
                        <div
                          className="bg-primary h-2 rounded-full"
                          style={{ width: `${(Number.parseInt(student.credits) / 120) * 100}%` }}
                        ></div>
                      </div>
                    </div>

                    <div className="grid grid-cols-2 gap-4">
                      <div className="bg-gray-100 rounded-lg p-4 text-center">
                        <p className="text-2xl font-bold">{student.gpa}</p>
                        <p className="text-sm text-gray-500">Current GPA</p>
                      </div>
                      <div className="bg-gray-100 rounded-lg p-4 text-center">
                        <p className="text-2xl font-bold">6</p>
                        <p className="text-sm text-gray-500">Semesters Remaining</p>
                      </div>
                    </div>
                  </div>
                </CardContent>
              </Card>
            </TabsContent>

            {/* Documents Tab */}
            <TabsContent value="documents" className="mt-6">
              <Card>
                <CardHeader>
                  <div className="flex items-center justify-between">
                    <div>
                      <CardTitle>Documents</CardTitle>
                      <CardDescription>Your uploaded documents and forms</CardDescription>
                    </div>
                    <Button>Upload Document</Button>
                  </div>
                </CardHeader>
                <CardContent>
                  <div className="space-y-4">
                    {student.documents.map((doc) => (
                      <div key={doc.id} className="flex items-center p-3 border rounded-lg hover:bg-gray-50">
                        <div className="p-2 bg-gray-100 rounded">
                          <FileText className="h-6 w-6 text-gray-500" />
                        </div>
                        <div className="ml-4 flex-1">
                          <p className="font-medium">{doc.name}</p>
                          <p className="text-sm text-gray-500">
                            Uploaded on {doc.date} • {doc.size}
                          </p>
                        </div>
                        <Button variant="ghost" size="sm">
                          Download
                        </Button>
                      </div>
                    ))}
                  </div>
                </CardContent>
              </Card>
            </TabsContent>
          </Tabs>
        </div>
      </main>

      {/* Footer */}
      <footer className="bg-white border-t p-6 md:p-8 mt-8">
        <div className="max-w-7xl mx-auto">
          <div className="grid md:grid-cols-3 gap-8">
            <div>
              <h3 className="font-semibold mb-3">Contact Information</h3>
              <address className="not-italic text-sm text-gray-600 space-y-1">
                <p>University Student Services</p>
                <p>123 Campus Drive</p>
                <p>Email: support@university.edu</p>
                <p>Phone: (555) 123-4567</p>
              </address>
            </div>
            <div>
              <h3 className="font-semibold mb-3">Quick Links</h3>
              <ul className="text-sm space-y-2">
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Academic Calendar
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Student Handbook
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    Library Resources
                  </Link>
                </li>
                <li>
                  <Link href="#" className="text-gray-600 hover:text-primary">
                    IT Support
                  </Link>
                </li>
              </ul>
            </div>
            <div>
              <h3 className="font-semibold mb-3">Connect With Us</h3>
              <div className="flex gap-4">
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"></path>
                  </svg>
                  <span className="sr-only">Facebook</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <rect x="2" y="2" width="20" height="20" rx="5" ry="5"></rect>
                    <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"></path>
                    <line x1="17.5" y1="6.5" x2="17.51" y2="6.5"></line>
                  </svg>
                  <span className="sr-only">Instagram</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M22 4s-.7 2.1-2 3.4c1.6 10-9.4 17.3-18 11.6 2.2.1 4.4-.6 6-2C3 15.5.5 9.6 3 5c2.2 2.6 5.6 4.1 9 4-.9-4.2 4-6.6 7-3.8 1.1 0 3-1.2 3-1.2z"></path>
                  </svg>
                  <span className="sr-only">Twitter</span>
                </Link>
                <Link href="#" className="text-gray-600 hover:text-primary">
                  <svg
                    xmlns="http://www.w3.org/2000/svg"
                    width="24"
                    height="24"
                    viewBox="0 0 24 24"
                    fill="none"
                    stroke="currentColor"
                    strokeWidth="2"
                    strokeLinecap="round"
                    strokeLinejoin="round"
                    className="h-5 w-5"
                  >
                    <path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"></path>
                    <rect x="2" y="9" width="4" height="12"></rect>
                    <circle cx="4" cy="4" r="2"></circle>
                  </svg>
                  <span className="sr-only">LinkedIn</span>
                </Link>
              </div>
              <div className="mt-4 text-sm text-gray-600">
                <p>© 2025 University Name. All rights reserved.</p>
                <div className="mt-2 flex gap-4">
                  <Link href="#" className="hover:text-primary">
                    Privacy Policy
                  </Link>
                  <Link href="#" className="hover:text-primary">
                    Terms of Use
                  </Link>
                </div>
              </div>
            </div>
          </div>
        </div>
      </footer>
    </div>
  )
}

